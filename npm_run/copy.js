const copy = require('copy');

copy('src/images/*', 'assets/images', function(err, file) {
    // exposes the vinyl `file` created when the file is copied
});

copy('src/fonts/**', 'assets/fonts', function(err, file) {
    // exposes the vinyl `file` created when the file is copied
});