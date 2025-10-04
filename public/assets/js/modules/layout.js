document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('custom-loader')) {
        const loader = document.createElement('div');
        loader.id = 'custom-loader';
        loader.innerHTML = `@include('components.loader')`;
        document.body.appendChild(loader);
    }
    document.getElementById('custom-loader').style.display = 'flex';
    document.body.classList.add('no-scroll');
});
