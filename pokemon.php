<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <!--<meta http-equiv="refresh" content="10"> -->
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, user-scalable=no">
	<title>PokemonGo Stats</title>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
	<style type="text/css" class="init">
	        body {
            padding-top: 5rem;
          }
          td.details-control {
              background: url('images/details_open.png') no-repeat center center;
              cursor: pointer;
          }
          tr.shown td.details-control {
              background: url('images/details_close.png') no-repeat center center;
          }
	</style>
	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js "></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-moment.js"></script>
	<script type="text/javascript" language="javascript" class="init">

    $(document).ready(function() {
		$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss' );
		
      var table = $('#logEntries').DataTable({
        "processing": true,
        "ajax": {
          "url": 'ajax/pokemon.php'
        },
        "search": {
          "regex": true
        },
        "aoColumns": [
          { mData: 'PokemonName' },
          { mData: 'Caught' },
          { mData: 'Shiny' },
          { mData: 'ShinyRate' }
        ]
      });
 
		$('#vis-buttons button').on( 'click', function (e) {
			e.preventDefault();
	 
			// Get the column API object
			var column = table.column( $(this).attr('data-column') );
	 
			// Toggle the visibility
			column.visible( ! column.visible() );
		});
    });
    
    function autoRefresh(){
      var table = $('#logEntries').DataTable();
      table.ajax.reload(null,false);
    }
    setInterval('autoRefresh()', 300000); 
	</script>
</head>
<body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="/">PokemonGoStats</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
  
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
				<a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
				<a class="nav-link" href="/pokemon.php">Pokemon</a>
            </li>
          </ul>
  
        </div>
      </nav>
	
	<div class="container-fluid">
	<h2>Pokemon in the last 24 hours</h2><br />
	<div id="vis-buttons">
        Toggle column: 	<button type="button" class="btn btn-outline-secondary" data-column="0">Pokemon Name</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="1">Caught</button>
						<button type="button" class="btn btn-outline-secondary" data-column="3">Shiny</button>
						<button type="button" class="btn btn-outline-secondary" data-column="3">Shiny Rate</button>
    </div><br />
      <table id="logEntries" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th >Pokemon Name</th>
            <th >Caught</th>
            <th >Shiny</th>
            <th >Shiny Rate</th>
          </tr>
        </thead>
      </table>
	</div>
	<a href="https://paypal.me/pools/c/8nDB1mCCQz"><img src="images/paypal_donate.jpg" style="width:300px;height:75px;"/></a>
	</body>
</html>