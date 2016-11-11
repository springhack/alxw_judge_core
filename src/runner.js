const fs = require('fs');
const vm = require('vm');

const readline = function () {
    return queue[ptr++];
}

let code = fs.readFileSync(process.argv.pop(), 'utf-8');
let ptr = 0;
let buffer = ''
let queue = [];
let FakeConsole = {
    log : (...rest) => console.log(...rest)
};

let main = () => {
    vm.runInNewContext(code, {
        console : FakeConsole,
        readline : readline
    });
}

process.stdin.setEncoding('utf8');
process.stdin.on('readable', () => {
    let chunk;
    while (null != (chunk = process.stdin.read()))
        buffer += chunk;
});
process.stdin.on('end', () => {
    queue = buffer.split('\n').filter(i => i != '');
    main()
});

