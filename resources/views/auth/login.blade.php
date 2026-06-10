<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
        :root {
            color-scheme: dark;
            --panel: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.08);
            --text: #e5eefc;
            --muted: #9fb2d0;
            --accent: #4fd1c5;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background: linear-gradient(180deg, #09101c 0%, #050b15 100%);
        }
        .card {
            width: min(420px, calc(100% - 24px));
            padding: 28px;
            border-radius: 28px;
            border: 1px solid var(--border);
            background: var(--panel);
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.38);
        }
        h1 { margin: 0 0 8px; }
        p { margin: 0 0 18px; color: var(--muted); }
        label { display: block; margin: 14px 0 8px; font-weight: 700; }
        input {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: #050b15;
            color: var(--text);
        }
        button {
            width: 100%;
            margin-top: 18px;
            padding: 14px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent), #2dd4bf);
            color: #031018;
            font-weight: 800;
            cursor: pointer;
        }
        .error {
            margin-top: 14px;
            color: #ffdbe1;
            background: rgba(251, 113, 133, 0.12);
            border: 1px solid rgba(251, 113, 133, 0.28);
            border-radius: 14px;
            padding: 12px 14px;
        }
        .hint { margin-top: 14px; font-size: 13px; color: var(--muted); }

        a {
            color: var(--accent);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <form class="card" method="POST" action="{{ route('login.store') }}">
        @csrf
        <h1>Sign in</h1>
        <p>Use your registered account to manage generated quizzes.</p>

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', 'test@example.com') }}" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" value="password" required>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <button type="submit">Login</button>
        <div class="hint">
            Don’t have an account?
            <a href="{{ route('register') }}">Register here</a>
        </div>
    </form>
</body>
</html>