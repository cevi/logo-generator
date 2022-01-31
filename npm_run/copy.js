const copyfiles = require('copyfiles');

copyfiles(['./src/images/*', './assets'], {
    'up': 1
}, (err) => {
    if (err) {
        console.log(err);
    }
});

copyfiles(['./src/fonts/**', './assets'], {
    'up': 1
}, (err) => {
    if (err) {
        console.log(err);
    }
});
