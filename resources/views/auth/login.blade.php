<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login | Refilling Monitoring System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --water-deep: #022c44;
            --water-main: #0369a1;
            --water-light: #e0f2fe;
            --water-accent: #0ea5e9;
            --water-accent-soft: #7dd3fc;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at top left, #f0f9ff 0, #e0f2fe 40%, #dbeafe 100%);
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 32px;
            padding: 48px;
            box-shadow: 0 25px 50px -12px rgba(3, 105, 161, 0.15);
            text-align: center;
        }

        .logo-pill {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: radial-gradient(circle at top, #0ea5e9 0, #0369a1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: white;
            margin: 0 auto 24px;
            box-shadow: 0 10px 20px rgba(3, 105, 161, 0.3);
            font-family: 'Outfit', sans-serif;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: var(--water-deep);
            margin: 0 0 8px;
        }

        p.subtitle {
            font-size: 15px;
            color: #64748b;
            margin: 0 0 32px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 14px 18px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            font-size: 15px;
            color: #1e293b;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: var(--water-accent);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: radial-gradient(circle at top, #0ea5e9 0, #0369a1 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 15px -3px rgba(3, 105, 161, 0.2);
            font-family: 'Outfit', sans-serif;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 20px -3px rgba(3, 105, 161, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-box {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
            text-align: left;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 32px;
        }

        .remember-wrap input {
            width: 18px;
            height: 18px;
            margin: 0;
            cursor: pointer;
        }

        .remember-wrap label {
            margin: 0;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
        }

        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: var(--water-accent);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-pill">RS</div>
        <h1>Admin Access</h1>
        <p class="subtitle">Secure login for Refilling Station Monitoring</p>

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ $rememberedEmail ?? old('email') }}" required autofocus placeholder="admin@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required placeholder="••••••••" style="padding-right: 50px;">
                    <button type="button" id="togglePassword" class="password-toggle" title="Show Password">
                        <span id="toggleIcon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </span>
                    </button>
                </div>
            </div>

            <div class="remember-wrap">
                <input type="checkbox" id="remember" name="remember" {{ isset($rememberedEmail) ? 'checked' : '' }}>
                <label for="remember">Keep me logged in</label>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'password') {
                        toggleIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`;
                        toggleBtn.title = 'Show Password';
                    } else {
                        toggleIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>`;
                        toggleBtn.title = 'Hide Password';
                    }
                });
            }
        });
    </script>
</body>
</html>
