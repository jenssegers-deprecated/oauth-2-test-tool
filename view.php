<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Oauth 2 test tool</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="#">Oauth 2 test tool</a>
          <a href="clear" class="btn btn-danger pull-right">Reset</a>
        </div>
      </div>
    </div>

    <div class="container">

  	<?php if($session->api_response): ?>
	  <div class="page-header">
      <h1>API response</h1>
      </div>
      
      <div class="well">
        <pre><?php echo $session->api_response; ?></pre>
      </div>
	  <?php endif;?>


	  <?php if($session->access_token_response): ?>	  
	  <div class="page-header">
      <h1>Test endpoints</h1>
      </div>
      
      <div class="well">
      	<form class="form-horizontal" method="post" action="api">
      		<div class="control-group">
	            <label class="control-label">Endpoint</label>
	            <div class="controls">
	              <input type="url" required name="api_endpoint" value="<?php echo $session->api_endpoint; ?>" class="input-xlarge">
	              <p class="help-block">The access_token parameter is automatically added</p>
	            </div>
	        </div>
	        
	        <div class="control-group">
	            <label class="control-label">Method</label>
	            <div class="controls">
	               <select name="api_method">
	                  <option value="get">GET</option>
	               	  <option value="post">POST</option>
	               </select>
			    </div>
	        </div>
	        
	        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Test endpoint</button>
            <button class="btn" type="reset">Cancel</button>
          </div>
        </form>
      </div>
	  
	  
	  
      <div class="page-header">
      <h1>Access token response</h1>
      </div>
      
      <div class="well">
        <pre><?php echo $session->access_token_response; ?></pre>
      </div>
      <?php endif; ?>
      
      
      
      <?php if($session->request_code): ?>
      <div class="page-header">
      <h1>Access token request</h1>
      </div>
      
      <div class="well">
      	<form class="form-horizontal" method="post" action="request">
      		<div class="control-group">
	            <label class="control-label">Code</label>
	            <div class="controls">
	              <input type="text" required name="request_code" value="<?php echo $session->request_code; ?>" class="input-xlarge">
	            </div>
	        </div>
	        
	        <div class="control-group">
	            <label class="control-label">Token request url</label>
	            <div class="controls">
	              <input type="text" required name="url_access_token" value="<?php echo $session->url_access_token; ?>" class="input-xlarge">
	            </div>
	        </div>
	        
	        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Request token</button>
            <button class="btn" type="reset">Cancel</button>
          </div>
        </form>
      </div>
      <?php endif; ?>
      


	  <div class="page-header">
      <h1>Client details</h1>
      </div>
      
      <div class="well">
      	<form class="form-horizontal" method="post" action="authorize">
      		<div class="control-group">
	            <label class="control-label">Client id</label>
	            <div class="controls">
	              <input required type="text" name="client_id" value="<?php echo $session->client_id; ?>" class="input-xlarge">
	            </div>
	        </div>
            
            <div class="control-group">
	            <label class="control-label">Client secret</label>
	            <div class="controls">
	              <input required type="text" name="client_secret" value="<?php echo $session->client_secret; ?>" class="input-xlarge">
	            </div>
	        </div>
	        
	        <div class="control-group">
	            <label class="control-label">Authorisation url</label>
	            <div class="controls">
	              <input required type="url" name="url_authorize" value="<?php echo $session->url_authorize; ?>" class="input-xlarge">
	            </div>
	        </div>
	        
	        <div class="control-group">
	            <label class="control-label">Callback url</label>
	            <div class="controls">
	              <span class="input-xlarge uneditable-input"><?php echo $callback_url; ?></span>
	              <p class="help-block">Add this callback url to your service's control panel</p>
	            </div>
	        </div>
	        
	        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Start authentication</button>
            <button class="btn" type="reset">Cancel</button>
          </div>
        </form>
      </div>
      
      
    </div> <!-- /container -->

  </body>
</html>
