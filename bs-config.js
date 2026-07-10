// BrowserSync dev config — live-reload in front of the CodeIgniter app.
// Run with: npm run dev  →  opens http://localhost:3000 proxying the Apache vhost.
module.exports = {
    proxy: "http://tweezzo.local",   // existing Apache vhost (see application/config/config.php)
    files: [
        "application/views/**/*.php",
        "application/controllers/**/*.php",
        "assets/css/**/*.css",
        "assets/js/**/*.js"
    ],
    reloadDelay: 150,
    open: false,
    notify: false
};
