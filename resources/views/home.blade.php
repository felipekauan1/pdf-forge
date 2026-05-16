@extends('layouts.app')

@section('content')
<nav>
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Compress</a>
    <a href="{{ route('merge') }}" class="{{ request()->routeIs('merge') ? 'active' : '' }}">Merge</a>
    <a href="{{ route('split') }}" class="{{ request()->routeIs('split') ? 'active' : '' }}">Split</a>
    <a href="{{ route('pdf-to-image') }}" class="{{ request()->routeIs('pdf-to-image') ? 'active' : '' }}">PDF → Imagem</a>
    <a href="{{ route('image-to-pdf') }}" class="{{ request()->routeIs('image-to-pdf') ? 'active' : '' }}">Imagem → PDF</a>
</nav>

<div class="card">
    <h2>Comprimir PDF</h2>
    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="operation" value="compress">
        <label>Selecione um PDF</label>
        <input type="file" name="files[0]" accept=".pdf" required>
        <button type="submit">Comprimir</button>
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
        const taskId = data.task_id;

        result.innerHTML = '⏳ Processando em background...';

        // Polling — verifica o status a cada 2 segundos
        const interval = setInterval(async () => {
            const statusRes = await fetch(`/api/pdf/status/${taskId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const statusData = await statusRes.json();

            if (statusData.status === 'done') {
                clearInterval(interval);
                result.className = 'result done';
                result.innerHTML = `✅ Pronto! <a href="/api/pdf/download/${taskId}">Baixar arquivo</a>`;
            } else if (statusData.status === 'failed') {
                clearInterval(interval);
                result.className = 'result error';
                result.innerHTML = `❌ Erro: ${statusData.error}`;
            }
        }, 2000);
    });
</script>
@endsection
