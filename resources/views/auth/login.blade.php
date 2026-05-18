<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - La Parrilla</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --carbon: #080403;
            --madera: #2D130C;
            --madera-rojiza: #672014;
            --rojo-parrilla: #B62128;
            --rojo-oscuro: #8C1A1F;
            --carne: #CC685D;
            --blanco-humo: #EDEDEC;
            --gris-metal: #BDB0A7;
            --gris-acero: #746E6A;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(182, 33, 40, 0.28), transparent 34%),
                radial-gradient(circle at bottom right, rgba(204, 104, 93, 0.18), transparent 35%),
                linear-gradient(135deg, var(--carbon), var(--madera), var(--madera-rojiza));
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(237, 237, 236, 0.97);
            border-radius: 28px;
            padding: 34px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(189, 176, 167, 0.45);
        }

        .login-logo {
            width: 145px;
            height: auto;
            display: block;
            margin: 0 auto 18px auto;
        }

        .login-title {
            text-align: center;
            font-size: 34px;
            font-weight: 900;
            color: var(--rojo-parrilla);
            margin-bottom: 6px;
        }

        .login-subtitle {
            text-align: center;
            color: var(--gris-acero);
            font-size: 15px;
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 700;
            color: var(--madera);
        }

        .form-control {
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid var(--gris-metal);
            background: #fff;
        }

        .form-control:focus {
            border-color: var(--rojo-parrilla);
            box-shadow: 0 0 0 .2rem rgba(182, 33, 40, .18);
        }

        .form-check-label {
            color: var(--madera);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--rojo-parrilla), var(--rojo-oscuro));
            color: var(--blanco-humo);
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 800;
            width: 100%;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--rojo-oscuro), var(--madera-rojiza));
            color: var(--blanco-humo);
        }

        .login-footer {
            margin-top: 22px;
            text-align: center;
            color: var(--gris-acero);
            font-size: 13px;
        }

        .error-text {
            color: var(--rojo-parrilla);
            font-size: 14px;
            margin-top: 6px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 26px;
                border-radius: 22px;
            }

            .login-logo {
                width: 125px;
            }

            .login-title {
                font-size: 30px;
            }
        }

        .btn-install-app {
            margin-top: 14px;
            width: 100%;
            border: 1px solid var(--rojo-parrilla);
            background: transparent;
            color: var(--rojo-parrilla);
            border-radius: 14px;
            padding: 11px;
            font-weight: 800;
        }

        .btn-install-app:hover {
            background: var(--rojo-parrilla);
            color: var(--blanco-humo);
        }
    </style>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#B62128">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="La Parrilla">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-la-parrilla.png') }}">
</head>

<body>

    <main class="login-page">

        <div class="login-card">

            <img src="{{ asset('images/logo-la-parrilla.png') }}" alt="Carnicería La Parrilla" class="login-logo">

            <h1 class="login-title">
                La Parrilla
            </h1>

            <p class="login-subtitle">
                Sistema interno de recepción de mercadería
            </p>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Correo electrónico
                    </label>

                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required
                        autofocus autocomplete="username">

                    @error('email')
                        <div class="error-text">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Contraseña
                    </label>

                    <input type="password" name="password" class="form-control" required
                        autocomplete="current-password">

                    @error('password')
                        <div class="error-text">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">

                    <label for="remember_me" class="form-check-label">
                        Recordarme
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    Iniciar sesión
                </button>
            </form>
            <button id="installAppBtn" class="btn-install-app d-none" type="button">
                Instalar aplicación en este teléfono
            </button>
            <div class="login-footer">
                Carnicería La Parrilla · Sistema privado
            </div>

        </div>

    </main>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function() {
                        console.log('Service Worker registrado correctamente');
                    })
                    .catch(function(error) {
                        console.log('Error registrando Service Worker:', error);
                    });
            });
        }
    </script>
    <script>
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();

            deferredPrompt = e;

            const installBtn = document.getElementById('installAppBtn');

            if (installBtn) {
                installBtn.classList.remove('d-none');

                installBtn.addEventListener('click', async function() {
                    installBtn.classList.add('d-none');

                    deferredPrompt.prompt();

                    const choiceResult = await deferredPrompt.userChoice;

                    deferredPrompt = null;
                });
            }
        });
    </script>
</body>

</html>
