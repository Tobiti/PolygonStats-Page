<?php 
	require('ajax/config.php');
	$stmt = $mysqli->prepare("SELECT * FROM Account WHERE Id=?");

	$stmt->bind_param("i", $_GET["id"]);
	$stmt->execute();
	$result = $stmt->get_result() or die("database error:". mysqli_error($mysqli));
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$data = $row;
	}
?>
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
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js "></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-moment.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" class="init">
	
    $(document).ready(function() {
		$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss' );
		
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
          { mData: 'Rockets' },
          { mData: 'Raids' },
          { mData: 'XpHour1' },
          { mData: 'XpHour24' },
          { mData: 'TotalXp' },
          { mData: 'StardustHour1' },
          { mData: 'StardustHour24' },
          { mData: 'TotalStardust' },
          { mData: 'StartTime' },
          { mData: 'TotalMinutes' }
        ],
        "order": [[ 13, "desc" ]],
        "columnDefs": [
            {
              "render": function ( data, type, row ) {
                return moment.utc(data).local().format('DD/MM/YYYY HH:mm:ss');
              },
              "targets": 13
            }
          ],
        "fnDrawCallback": function( oSettings ) {
			$('#sessions tbody').off().on('click', 'tr', function () {
				var data = table.row( this ).data();
				if (myData.daily == 0) {
					window.location = "session.php?id="+data.SessionId;
				} else {
					window.location = "session.php?acc_id=<?php echo $_GET["id"] ?>&date="+encodeURIComponent(data.StartTime);
				}
			});
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
        <a class="navbar-brand" href="index.html">PokemonGoStats</a>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-labewl="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
        </button>
  
        <div class="collapse navbar-collapse" id="navbarToggler">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item active">
					<a class="nav-link" href="index.html">Home <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="pokemon.php">Pokemon</a>
				</li>
			</ul>
		</div>
	</nav>
	
	<div class="container-fluid">
    <h2><?php echo (isset($data) ? hideAccountName($data["Name"]) : "AccountName not Found") ?></h2>
		
	<?php 
		if(isset($data)){
			echo "<strong>Level:</strong> " . $data["Level"] . " <strong>Exp:</strong> " . $data["Experience"] . "/" . $data["NextLevelExp"] . " <strong>Stardust:</strong> " . $data["Stardust"] . " <strong>Pokecoins:</strong> " . $data["Pokecoins"] . " <strong>Team:</strong> " . $data["Team"] . "<br />";
		}
	?>
    <br />
	<div id="vis-buttons">
        Toggle column: 	<button type="button" class="btn btn-outline-secondary" data-column="0">Caught Pokemon</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="1">Escaped Pokemon</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="2">Shiny Pokemon</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="3">100IV</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="4">Spinned Pokestops</button>
						<button type="button" class="btn btn-outline-secondary" data-column="5">Rockets</button>
						<button type="button" class="btn btn-outline-secondary" data-column="6">Raids</button>
						<button type="button" class="btn btn-outline-secondary" data-column="7">XP/h</button>
						<button type="button" class="btn btn-outline-secondary" data-column="8">XP/Day</button>
						<button type="button" class="btn btn-outline-secondary" data-column="9">XP Total</button>
						<button type="button" class="btn btn-outline-secondary" data-column="10">Stardust/h</button>
						<button type="button" class="btn btn-outline-secondary" data-column="11">Stardust/Day</button>
						<button type="button" class="btn btn-outline-secondary" data-column="12">Stardust Total</button>
						<button type="button" class="btn btn-outline-secondary" data-column="13">Start Time</button>
						<button type="button" class="btn btn-outline-secondary" data-column="14">Total Minutes</button>
    </div><br />
	<div id="">
        Switch to: 	<button type="button" class="btn btn-outline-secondary" id="switch-to-daily">Daily</button>
					<button type="button" class="btn btn-outline-secondary" onclick="location.href = 'account_pokemon.php?id=<?php echo $_GET["id"] ?>';">Caught Pokemon</button>
    </div><br />
      <table id="sessions" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th style="white-space:normal;">Caught Pokemon</th>
            <th style="white-space:normal;">Escaped Pokemon</th>
            <th style="white-space:normal;">Shiny Pokemon</th>
            <th style="white-space:normal;">100IV</th>
            <th style="white-space:normal;">Spinned Pokestops</th>
            <th style="white-space:normal;">Rockets</th>
            <th style="white-space:normal;">Raids</th>
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
	<a href="https://paypal.me/tobiti22"><img src="images/paypal_donate.jpg" style="width:300px;height:75px;"/></a>
	</body>
</html>