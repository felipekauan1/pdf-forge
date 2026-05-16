<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Forge</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: system-ui, sans-serif;
            background: #f4f4f5;
            color: #18181b;
            min-height: 100vh;
        }

        header {
            background: #18181b;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        header h1 { font-size: 1.25rem; font-weight: 700; }
        header span { font-size: 0.8rem; color: #a1a1aa; }

        main {
            max-width: 680px;
            margin: 3rem auto;
            padding: 0 1rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }

        .card h2 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e4e4e7;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: #3f3f46;
        }

        input[type="file"] {
            width: 100%;
            padding: 0.6rem;
            border: 1px dashed #d4d4d8;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            background: #fafafa;
        }

        button[type="submit"] {
            background: #18181b;
            color: white;
            border: none;
            padding: 0.65rem 1.5rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
        }

        button[type="submit"]:hover { background: #27272a; }

        .result {
            margin-top: 1.25rem;
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            display: none;
        }

        .result.pending { background: #fef9c3; color: #854d0e; display: block; }
        .result.done    { background: #dcfce7; color: #166534; display: block; }
        .result.error   { background: #fee2e2; color: #991b1b; display: block; }

        .result a {
            display: inline-block;
            margin-top: 0.5rem;
            color: #166534;
            font-weight: 600;
            text-decoration: underline;
        }

        nav {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        nav a {
            padding: 0.4rem 0.9rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            background: white;
            color: #3f3f46;
            border: 1px solid #e4e4e7;
        }

        nav a.active, nav a:hover {
            background: #18181b;
            color: white;
            border-color: #18181b;
        }
    </style>
</head>
<body>
    <header>
        <h1>⚙️ PDF Forge</h1>
        <span>API de manipulação de PDFs</span>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
