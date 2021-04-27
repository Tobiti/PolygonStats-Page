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
	</style>
	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js "></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-moment.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" class="init">
	
    $(document).ready(function() {
		var myData = {};
		myData.account_id = "<?php echo $_GET["id"] ?>";
		myData.daily = 0
		
      var table = $('#sessions').DataTable({
        "processing": true,
        "ajax": {
          "url": 'ajax/account_sessions.php',
          "data":function(d) {
			return  $.extend(d, myData);
          }
        },
        "search": {
          "regex": true
        },
        "aoColumns": [
          { mData: 'CaughtPokemon' },
          { mData: 'FleetPokemon' },
          { mData: 'ShinyPokemon' },
          { mData: '100IV' },
          { mData: 'FortSpins' },
          { mData: 'XpHour1' },
          { mData: 'XpHour24' },
          { mData: 'TotalXp' },
          { mData: 'StardustHour1' },
          { mData: 'StardustHour24' },
          { mData: 'TotalStardust' },
          { mData: 'StartTime' },
          { mData: 'TotalMinutes' }
        ],
        "order": [[ 11, "desc" ]],
        "columnDefs": [
            {
              "render": function ( data, type, row ) {
                return moment.utc(data).local().format('DD/MM/YYYY HH:mm:ss');
              },
              "targets": 11
            }
          ],
        "fnDrawCallback": function( oSettings ) {
          $('#sessions tbody').off().on('click', 'tr', function () {
			  if (!myData.daily) {
				var data = table.row( this ).data();
				window.location = "session.php?id="+data.SessionId+"&account=<?php echo $_GET["account"] ?>";
			  }
          } );
        }
      });
 
		$('#vis-buttons button').on( 'click', function (e) {
			e.preventDefault();
	 
			// Get the column API object
			var column = table.column( $(this).attr('data-column') );
	 
			// Toggle the visibility
			column.visible( ! column.visible() );
		});
 
		$('#switch-to-daily').on( 'click', function (e) {
			e.preventDefault();
			myData.daily = myData.daily == 1 ? 0 : 1;
			var buttonText = document.getElementById("switch-to-daily").firstChild;
			buttonText.data = myData.daily == 1 ? "Session" : "Daily";
			table.ajax.reload(null,false);
		});
    });
    function autoRefresh(){
      var table = $('#sessions').DataTable();
      table.ajax.reload(null,false);
    }
    setInterval('autoRefresh()', 60000); 
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
				<a class="nav-link" href="/admin">Admin</a>
            </li>
          </ul>
  
        </div>
      </nav>
	
	<div class="container-fluid">
    <h2>Sessions for <?php echo $_GET["account"] ?></h2><br />
	<div id="vis-buttons">
        Toggle column: 	<button type="button" class="btn btn-outline-secondary" data-column="0">Caught Pokemon</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="1">Escaped Pokemon</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="2">Stardust Reward</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="3">Shiny Pokemon</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="4">100IV</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="5">Spinned Pokestops</button>
						<button type="button" class="btn btn-outline-secondary" data-column="6">XP/h</button>
						<button type="button" class="btn btn-outline-secondary" data-column="7">XP/Day</button>
						<button type="button" class="btn btn-outline-secondary" data-column="8">XP Total</button>
						<button type="button" class="btn btn-outline-secondary" data-column="9">Stardust/h</button>
						<button type="button" class="btn btn-outline-secondary" data-column="10">Stardust/Day</button>
						<button type="button" class="btn btn-outline-secondary" data-column="11">Stardust Total</button>
						<button type="button" class="btn btn-outline-secondary" data-column="12">Start Time</button>
						<button type="button" class="btn btn-outline-secondary" data-column="13">Total Minutes</button>
    </div><br />
	<div id="">
        Switch to: <button type="button" class="btn btn-outline-secondary" id="switch-to-daily">Daily</button>
    </div><br />
      <table id="sessions" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th style="white-space:normal;">Caught Pokemon</th>
            <th style="white-space:normal;">Escaped Pokemon</th>
            <th style="white-space:normal;">Shiny Pokemon</th>
            <th style="white-space:normal;">100IV</th>
            <th style="white-space:normal;">Spinned Pokestops</th>
            <th style="white-space:normal;">XP/h</th>
            <th style="white-space:normal;">XP/Day</th>
            <th >XP Total</th>
            <th style="white-space:normal;">Stardust/h</th>
            <th style="white-space:normal;">Stardust/Day</th>
            <th style="white-space:normal;">Stardust Total</th>
            <th style="white-space:normal;">Start Time</th>
            <th style="white-space:normal;">Total Minutes</th>
          </tr>
        </thead>
      </table>
	</div>
	<a href="https://paypal.me/pools/c/8nDB1mCCQz"><img src="images/paypal_donate.jpg" style="width:300px;height:75px;"/></a>
	</body>
</html>