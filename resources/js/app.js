const copyButton = document.getElementById('copy-api-url');
const apiBaseUrl = document.getElementById('api-base-url');

copyButton?.addEventListener('click', async () => {
    await navigator.clipboard.writeText(apiBaseUrl.textContent.trim());

    copyButton.textContent = 'Copied!';

    setTimeout(() => {
        copyButton.textContent = 'Copy';
    }, 2000);
});
