@extends('layouts.app')

@section('content')
<nav>
    <a href="{{ route('home') }}">Compress</a>
    <a href="{{ route('merge') }}">Merge</a>
    <a href="{{ route('split') }}" class="active">Split</a>
    <a href="{{ route('pdf-to-image') }}">PDF → Imagem</a>
    <a href="{{ route('image-to-pdf') }}">Imagem → PDF</a>
</nav>

<div class="card">
    <h2>Dividir PDF por páginas</h2>
    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="operation" value="split">
        <label>Selecione um PDF</label>
        <input type="file" name="files[0]" accept=".pdf" required>
        <button type="submit">Dividir</button>
    </form>
    <div id="result" class="result"></div>
</div>

<script>
    document.getElementById('uploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const result = document.getElementById('result');
        result.className = 'result pending';
        result.innerHTML = '⏳ Enviando arquivo...';

        const formData = new FormData(this);
        const res = await fetch('/api/pdf/upload', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData
        });

        const data = await res.json();
        result.innerHTML = '⏳ Dividindo páginas em background...';

        const interval = setInterval(async () => {
            const statusRes = await fetch(`/api/pdf/status/${data.task_id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const statusData = await statusRes.json();

            if (statusData.status === 'done') {
                clearInterval(interval);
                const pages = statusData.result_path.split(',');
                result.className = 'result done';
                result.innerHTML = `✅ ${pages.length} página(s) gerada(s)! <a href="/api/pdf/download/${data.task_id}">Baixar arquivos</a>`;
            } else if (statusData.status === 'failed') {
                clearInterval(interval);
                result.className = 'result error';
                result.innerHTML = `❌ Erro: ${statusData.error}`;
            }
        }, 2000);
    });
</script>
@endsection
