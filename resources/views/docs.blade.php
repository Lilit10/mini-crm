@extends('layouts.app')

@section('title', 'API Docs')

@section('content')
    <div class="card">
        <h1 style="margin:0 0 0.25rem;font-size:1.25rem;">API documentation</h1>
        <p class="muted" style="margin:0 0 1rem;">
            OpenAPI spec: <a href="/openapi.yaml" target="_blank" rel="noopener">openapi.yaml</a>
        </p>

        <div id="swagger-ui"></div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function () {
            window.ui = SwaggerUIBundle({
                url: '/openapi.yaml',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: 'BaseLayout'
            });
        };
    </script>
@endsection

