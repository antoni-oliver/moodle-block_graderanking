export const init = () => {
        document.querySelectorAll('.graderanking').forEach(div => {
            const row = div.querySelector('tr.me');
            if (!row) {
                return;
            }
            div.scrollTo({top: row.offsetTop - div.clientHeight / 2 + row.clientHeight / 2});
        });
};
