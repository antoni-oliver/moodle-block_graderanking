export const init = () => {
    document.querySelectorAll('.graderanking_container').forEach(div => {
        // We find the row with the "me" class.
        const row = div.querySelector('tr.me');
        if (!row) {
            return;
        }
        // If found, we center that on the container.
        div.scrollTo({top: row.offsetTop - div.clientHeight / 2 + row.clientHeight / 2});
    });
};
