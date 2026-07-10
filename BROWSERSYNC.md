# BrowserSync — Live Reload for Development

Auto-refresh the browser when you edit PHP views, controllers, JS, or CSS — no manual reload.

CodeIgniter has no built-in dev server, so we run **BrowserSync** as a proxy *in front of* the existing Apache app. The app keeps being served exactly as normal; BrowserSync just watches files and reloads the browser (and hot-injects CSS) on save.

## Prerequisites

- Node.js and npm installed.
- The app running under Apache at its vhost (`http://tweezzo.local` — see `application/config/config.php` → `$config['base_url']`).
- Dependencies installed: `npm install`.

## Usage

```bash
npm run dev
```

Then open **http://localhost:3000** and develop there. Saving any watched file reloads the browser automatically.

Stop it with `Ctrl+C`.

> Browse via `http://localhost:3000`, **not** `http://tweezzo.local` — the live-reload snippet is only injected through the proxy. Your normal vhost URL keeps working untouched.

## What gets watched

Configured in [`bs-config.js`](bs-config.js):

| Path | Purpose |
|------|---------|
| `application/views/**/*.php` | Page templates |
| `application/controllers/**/*.php` | Controllers |
| `assets/css/**/*.css` | Stylesheets (hot-injected without full reload) |
| `assets/js/**/*.js` | Scripts |

To watch more paths (e.g. `application/models/**/*.php`), add globs to the `files` array in `bs-config.js`.

## Configuration

`bs-config.js`:

```js
module.exports = {
    proxy: "http://tweezzo.local",   // must match $config['base_url'] in config.php
    files: [ /* watched globs */ ],
    reloadDelay: 150,                // ms delay before reload (lets PHP finish writing)
    open: false,                     // set true to auto-open the browser on start
    notify: false                    // set true to show the "Connected" overlay
};
```

**Keep `proxy` in sync with `base_url`.** If the vhost hostname changes in `application/config/config.php`, update `proxy` here to match.

## Notes & gotchas

- **This is a dev-only tool.** It is a `devDependency` and never runs in production.
- **CSS cache-busting can force full reloads.** Some layouts append a timestamp query to CSS URLs, e.g. `assets/css/adminlte_custom.min.css?<?php echo date('YmdHis');?>`. Because the URL changes every request, BrowserSync can't match the file for seamless injection and falls back to a full page reload. Everything still reloads correctly — you just lose the no-flash CSS swap on those specific files. Removing the timestamp query (in dev) restores instant CSS injection.
- **Port already in use?** BrowserSync uses `3000` (UI on `3001`). Pass `--port 3002` or add `port: 3002` to `bs-config.js` if something else holds the port.
