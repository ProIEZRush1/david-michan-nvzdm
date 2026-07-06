// Self-contained headless smoke test for the David Michan WhatsApp phone-line sales panel.
// Run with: node tests/e2e/smoke.mjs  (against a running `php artisan serve` instance)
import { chromium } from 'playwright';
import http from 'node:http';

const BASE_URL = process.env.SMOKE_BASE_URL ?? 'http://127.0.0.1:8137';
const GATEWAY_PORT = Number(process.env.SMOKE_GATEWAY_PORT ?? 3001);
const GATEWAY_TOKEN = process.env.GATEWAY_TOKEN ?? 'change-me';
const ADMIN_EMAIL = 'david-michan@overcloud.us';
const ADMIN_PASSWORD = '3WfHBI4dMjKR';

function fail(message) {
    console.error(`FAIL: ${message}`);
    process.exit(1);
}

// ---- fake WhatsApp gateway (captures whatever the bot tries to send) ----
const sentMessages = [];
const fakeGateway = http.createServer((req, res) => {
    if (req.method === 'GET' && req.url === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ ok: true }));
        return;
    }
    if (req.method === 'GET' && req.url === '/qr') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ status: 'disconnected', qrDataUrl: null, me: null }));
        return;
    }
    if (req.method === 'POST' && req.url === '/send') {
        if (req.headers['x-gateway-token'] !== GATEWAY_TOKEN) {
            res.writeHead(401);
            res.end();
            return;
        }
        let body = '';
        req.on('data', (chunk) => (body += chunk));
        req.on('end', () => {
            sentMessages.push(JSON.parse(body));
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ ok: true }));
        });
        return;
    }
    res.writeHead(404);
    res.end();
});

async function inbound(from, text, fromName = 'Cliente E2E') {
    const res = await fetch(`${BASE_URL}/api/wa/inbound`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'x-gateway-token': GATEWAY_TOKEN },
        body: JSON.stringify({ from, fromName, text, isGroup: false }),
    });
    if (!res.ok) fail(`webhook returned ${res.status} for text "${text}"`);
    const json = await res.json();
    if (!json.ok) fail(`webhook did not acknowledge message "${text}"`);
}

async function run() {
    await new Promise((resolve) => fakeGateway.listen(GATEWAY_PORT, '127.0.0.1', resolve));
    console.log(`Fake WhatsApp gateway listening on :${GATEWAY_PORT}`);

    const browser = await chromium.launch(
        process.env.SMOKE_CHROMIUM_PATH ? { executablePath: process.env.SMOKE_CHROMIUM_PATH } : {},
    );
    const page = await browser.newPage();

    try {
        // ---- 1. login ----
        await page.goto(`${BASE_URL}/login`);
        await page.fill('#email', ADMIN_EMAIL);
        await page.fill('#password', ADMIN_PASSWORD);
        await page.click('button[type="submit"]');
        await page.waitForURL(`${BASE_URL}/dashboard`, { timeout: 15000 });
        const dashboardText = await page.textContent('body');
        if (!dashboardText.includes('David Michan')) fail('dashboard does not show business name "David Michan"');
        if (dashboardText.toLowerCase().includes("you're logged in")) fail('generic Breeze "You\'re logged in" text found');
        console.log('OK: login and dashboard branding');

        // ---- 2. CRUD modules: create via UI, verify listed, reload, verify persists ----
        const stamp = Date.now() % 100000;

        // Planes
        await page.goto(`${BASE_URL}/planes`);
        await page.click('text=+ Nuevo plan');
        await page.fill('#nombre', `Plan E2E ${stamp}`);
        await page.fill('#precio', '199.00');
        await page.click('button[type="submit"]');
        await page.waitForURL(`${BASE_URL}/planes`);
        let bodyText = await page.textContent('body');
        if (!bodyText.includes(`Plan E2E ${stamp}`)) fail('new plan not listed after create');
        await page.reload();
        bodyText = await page.textContent('body');
        if (!bodyText.includes(`Plan E2E ${stamp}`)) fail('plan did not persist after reload');
        console.log('OK: Planes create + persist');

        // Números (inventory)
        await page.goto(`${BASE_URL}/numeros`);
        await page.click('text=+ Agregar número');
        const numeroValor = `+52 55 9${stamp}`;
        await page.fill('#numero', numeroValor);
        await page.click('button[type="submit"]');
        await page.waitForURL(`${BASE_URL}/numeros`);
        bodyText = await page.textContent('body');
        if (!bodyText.includes(numeroValor)) fail('new number not listed after create');
        await page.reload();
        bodyText = await page.textContent('body');
        if (!bodyText.includes(numeroValor)) fail('number did not persist after reload');
        console.log('OK: Números create + persist');

        // Clientes
        await page.goto(`${BASE_URL}/clientes`);
        await page.click('text=+ Nuevo cliente');
        const clienteTelefono = `52155${stamp}0`;
        await page.fill('#nombre', `Cliente E2E ${stamp}`);
        await page.fill('#telefono', clienteTelefono);
        await page.click('button[type="submit"]');
        await page.waitForURL(`${BASE_URL}/clientes`);
        bodyText = await page.textContent('body');
        if (!bodyText.includes(`Cliente E2E ${stamp}`)) fail('new cliente not listed after create');
        await page.reload();
        bodyText = await page.textContent('body');
        if (!bodyText.includes(`Cliente E2E ${stamp}`)) fail('cliente did not persist after reload');
        console.log('OK: Clientes create + persist');

        // Pedidos
        await page.goto(`${BASE_URL}/pedidos`);
        await page.click('text=+ Nuevo pedido');
        const pedidoTelefono = `52155${stamp}1`;
        await page.fill('#cliente', `Pedido E2E ${stamp}`);
        await page.fill('#telefono', pedidoTelefono);
        await page.click('button[type="submit"]');
        await page.waitForURL(`${BASE_URL}/pedidos`);
        bodyText = await page.textContent('body');
        if (!bodyText.includes(`Pedido E2E ${stamp}`)) fail('new pedido not listed after create');
        await page.reload();
        bodyText = await page.textContent('body');
        if (!bodyText.includes(`Pedido E2E ${stamp}`)) fail('pedido did not persist after reload');
        console.log('OK: Pedidos create + persist');

        // ---- 3. WhatsApp webhook: an inbound message must make the bot reply ----
        const from = `52155${stamp}9`;
        await inbound(from, 'hola');
        if (sentMessages.length === 0) fail('bot did not send any reply to the greeting');
        if (!sentMessages[sentMessages.length - 1].text.includes('Plan E2E')) {
            // Plan list should include at least the seeded plans; just check it's a plan list.
            if (!/plan/i.test(sentMessages[sentMessages.length - 1].text)) {
                fail('bot greeting reply does not look like a plan list');
            }
        }
        console.log('OK: webhook inbound message produced a bot reply (greeting/plan list)');

        await inbound(from, '1');
        await inbound(from, 'si');
        const lastMessage = sentMessages[sentMessages.length - 1];
        if (!lastMessage || lastMessage.to !== from) fail('bot did not reply to the order confirmation');
        if (!/(número asignado|asesor te asigna)/i.test(lastMessage.text)) {
            fail(`unexpected final bot reply: ${lastMessage?.text}`);
        }
        console.log('OK: full webhook conversation confirmed the order and the bot replied with the assigned number');

        // Verify the order created by the bot shows up in the admin panel.
        await page.goto(`${BASE_URL}/pedidos`);
        bodyText = await page.textContent('body');
        if (!bodyText.includes(from)) fail('bot-created pedido not visible in the admin Pedidos list');
        console.log('OK: bot-created pedido visible in the admin panel (webhook → DB → UI)');

        // ---- 4. anti-genérico checks (plain HTTP fetch — no rendering needed) ----
        for (const url of [`${BASE_URL}/login`]) {
            const html = await (await fetch(url)).text();
            if (/laravel/i.test(html)) fail(`page ${url} still contains the word "Laravel"`);
        }
        console.log('OK: no "Laravel" branding leaked into the login page');

        console.log('\nALL SMOKE CHECKS PASSED');
    } finally {
        await browser.close();
        fakeGateway.close();
    }
}

run().catch((err) => {
    console.error(err);
    process.exit(1);
});
