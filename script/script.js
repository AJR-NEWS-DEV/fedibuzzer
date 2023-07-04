const design = `<style>
    .fs-sns {
        display: flex;
    }
    .fs-sns a {
        flex-grow: 1;
        display: block;
        color: #fff;
        padding: 0.3em 0 0.2em;
        text-align: center;
        font-size: 1.3em;
    }</style>
    <div>

    </div>`;

window.addEventListener("load", () => {
    // inject share button element
    document.currentScript.insertAdjacentHTML("afterend", design);
});