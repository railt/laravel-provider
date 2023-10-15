<?php
/**
 * @see https://github.com/graphql/graphiql/tree/graphiql%403.0.5/examples/graphiql-cdn
 * @var $this \Railt\SymfonyBundle\Controller\PlaygroundRequestHandler
 */
?>
<!doctype html>
<html lang="en">
<head>
    <title>GraphiQL</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            width: 100%;
            overflow: hidden;
        }

        #graphiql {
            height: 100vh;
        }
    </style>

    <script crossorigin src="//unpkg.com/react@18/umd/react.development.js"></script>
    <script crossorigin src="//unpkg.com/react-dom@18/umd/react-dom.development.js"></script>

    <script src="//unpkg.com/graphiql/graphiql.min.js" type="application/javascript"></script>
    <link rel="stylesheet" href="//unpkg.com/graphiql/graphiql.min.css" />

    <script src="//unpkg.com/@graphiql/plugin-explorer/dist/index.umd.js" crossorigin></script>
    <link rel="stylesheet" href="//unpkg.com/@graphiql/plugin-explorer/dist/style.css"/>
</head>

<body>
<div id="graphiql">Loading...</div>
<script>
    const fetcher = GraphiQL.createFetcher({
        url: '<?=$this->route?>',
        headers: {
<?php foreach ($this->headers as $name => $value): ?>
            <?=\var_export($name, true)?>: <?=\var_export($value, true)?>,
<?php endforeach; ?>
        },
    });

    ReactDOM.createRoot(document.getElementById('graphiql'))
        .render(React.createElement(GraphiQL, {
            fetcher,
            defaultEditorToolsVisibility: true,
            plugins: [GraphiQLPluginExplorer.explorerPlugin()],
        }));
</script>
</body>
</html>
