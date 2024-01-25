export default function ()
{
    document.addEventListener("DOMContentLoaded", function () {
        const elements = document.querySelectorAll("a.action-delete");

        elements.forEach((item) => {
            item.addEventListener('click', (event) => {
                event.preventDefault();
                const confirmLink = document.querySelector("#modal-confirm-action");
                confirmLink.setAttribute("href",  event.currentTarget.href);
            });
        });
    });
}
