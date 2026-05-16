@extends('layouts.app')

@section('content')
<nav>
    <a href="{{ route('home') }}">Compress</a>
    <a href="{{ route('merge') }}" class="active">Merge</a>
    <a href="{{ route('split') }}">Split</a>
    <a href="{{ route('pdf-to-image') }}">PDF → Imagem</a>
    <a href="{{ route('image-to-pdf') }}">Imagem → PDF</a>
</nav>

<div class="card">
    <h2>Unir PDFs</h2>
    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="operation" value="merge">
        <label>Selecione o primeiro PDF</label>
        <input type="file" name="files[0]" accept=".pdf" required>
        <label>Selecione o segundo PDF</label>
        <input type="file" name="files[1]" accept=".pdf" required>
        <button type="submit">Unir PDFs</button>
    </form>
    <div id="result" class="result"></div>
</div>

<script>
    document.getElementById('uploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const result = document.getElementById('result');
        result.className = 'result pending';
        result.innerHTML = '⏳ Enviando arquivos...';

        const formData = new FormData(this);
        const res = await fetch('/api/pdf/upload', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData
        });

        const data = await res.json();
        result.innerHTML = '⏳ Processando em background...';

        const interval = setInterval(async () => {
            const statusRes = await fetch(`/api/pdf/status/${data.task_id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const statusData = await statusRes.json();

            if (statusData.status === 'done') {
                clearInterval(interval);
                result.className = 'result done';
                result.innerHTML = `✅ Pronto! <a href="/api/pdf/download/${data.task_id}">Baixar PDF unido</a>`;
            } else if (statusData.status === 'failed') {
                clearInterval(interval);
                result.className = 'result error';
                result.innerHTML = `❌ Erro: ${statusData.error}`;
            }
        }, 2000);
    });
</script>
@endsection
