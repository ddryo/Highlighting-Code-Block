document.addEventListener('DOMContentLoaded', function() {
    // Token customize
    const keyToken = document.querySelectorAll('.token.keyword');
    for (let i = 0; i < keyToken.length; i++) {
        const elem = keyToken[i];

        const defArr = ['function', 'def', 'class'];
        if (-1 !== defArr.indexOf(elem.textContent)) {
            elem.classList.add('def');
        } else if ('this' === elem.textContent) {
            elem.classList.add('this');
        }
    }

    // Line highlighter position.
    const lineHighlighter = document.querySelectorAll('.line-highlight');
    for (let i = 0; i < lineHighlighter.length; i++) {
        const elem = lineHighlighter[i];
        if (!elem.parentNode.classList.contains('line-numbers')) {
            const dataStart = elem.getAttribute('data-start');

            let topPosEm = (dataStart - 1) * 1.5; //line-heightの値

            // Line highlighterの位置がずれるので桁数に応じて微調節 -> ずれなくなった？
            // if (5 < dataStart) {
            //     topPosEm = topPosEm - dataStart * 0.02;
            // }
            // if (10 < dataStart) {
            //     topPosEm = topPosEm - dataStart * 0.01;
            // }
            // if (20 < dataStart) {
            //     topPosEm = topPosEm - dataStart * 0.001;
            // }

            elem.style.top = topPosEm + 'em';
        }
    }
});
