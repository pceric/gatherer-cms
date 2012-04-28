<h1>An error occurred</h1>
<h2>{$view->message}</h2>
 
{if $view->exception}
 
<h3>Exception information:</h3>
<p>
    <b>Message:</b> {$view->exception->getMessage()}
</p>
 
<h3>Stack trace:</h3>
<pre>{$view->exception->getTraceAsString()}
</pre>
 
<h3>Request Parameters:</h3>
<pre>{var_export($view->request->getParams(), true)}
</pre>
{/if}
