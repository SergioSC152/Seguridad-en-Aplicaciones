@auth
    <script>
        // Un documento restaurado del historial debe volver a comprobar la sesión en el servidor.
        window.addEventListener('pagehide', function () {
            document.documentElement.style.visibility = 'hidden';
        });
        window.addEventListener('pageshow', function (event) {
            const navigation = performance.getEntriesByType('navigation')[0];
            if (event.persisted || navigation?.type === 'back_forward') {
                window.location.reload();
                return;
            }
            document.documentElement.style.visibility = '';
        });
    </script>
@endauth
