const { execFileSync } = require('child_process');
module.exports = async () => {
    execFileSync('php', ['artisan', 'migrate', '--env=testing', '--force'], {stdio:'inherit'});
    execFileSync('php', ['artisan', 'db:seed', '--env=testing', '--class=BrowserTestSeeder', '--force'], {stdio:'inherit'});
};
