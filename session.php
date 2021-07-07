<?php 
	require('ajax/config.php');

	if (isset($_GET["id"])) {
		$id = $_GET["id"];
		$stmt = $mysqli->prepare("SELECT * FROM Account WHERE Id=(SELECT s.AccountId FROM Session s WHERE s.Id=?)");
	} else {
		$id = $_GET["acc_id"];
		$stmt = $mysqli->prepare("SELECT * FROM Account WHERE Id=?");
	}
	
	$stmt->bind_param("i", $id);
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
		
	function getFormatedPokemonName(name) {
	  switch (name.toLowerCase()) {
		case "mrmime":
		  return "mr-mime";
		case "mrrime":
		  return "mr-rime";
		case "mimejr":
		  return "mime-jr";
		default:
		  return name.toLowerCase().replace("female", "-f").replace("male", "-m");
	  }
	}

	function getGenerationIdentifier(name) {
	  switch (name.toLowerCase()) {
		case "mrrime":
		  return "go";
		default:
		  return "bank";
	  }
	}
	
    /* Formatting function for row details - modify as you need */
    function format ( d ) {
        // `d` is the original data object for the row
        return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
          '<tr>'+
            '<td><a href="http://pokemondb.net/pokedex/'+ getFormatedPokemonName(d.PokemonName) +'"><img src="https://img.pokemondb.net/sprites/'+ getGenerationIdentifier(d.PokemonName) +'/'+ ((d.Shiny == 1) ? 'shiny':'normal') +'/'+ getFormatedPokemonName(d.PokemonName) +'.png" alt="'+d.PokemonName+'"></a></td>'+
            '<td>'+
              '<table>'+
                '<tr>'+
                    '<td>Pokemon:</td>'+
                    '<td>'+d.PokemonName+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Candy:</td>'+
                    '<td>'+d.CandyAwarded+'</td>'+
                '</tr>'+
              '</table>'+
            '</td>'+
            '<td>'+
              '<table>'+
                '<tr>'+
                    '<td>Attack:</td>'+
                    '<td>'+d.Attack+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Defense:</td>'+
                    '<td>'+d.Defense+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Stamina:</td>'+
                    '<td>'+d.Stamina+'</td>'+
                '</tr>'+
              '</table>'+
            '</td>'+
          '</tr>'+
        '</table>';
    }

    $(document).ready(function() {
		$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss' );
		
      var table = $('#logEntries').DataTable({
		"pageLength": 25,
        "processing": true,
        "ajax": {
          "url": 'ajax/session_log_entries.php',
          "data": {
			  <?php 
				if (isset($_GET["id"])) {
					echo "\"session_id\": ".$_GET["id"]."\n";
				} else {
					echo "\"date\": \"".$_GET["date"]."\",\n";
					echo "\"acc_id\": \"".$_GET["acc_id"]."\"\n";
				}
			  ?>
          }
        },
        "search": {
          "regex": true
        },
        "aoColumns": [
          {
            "className": 'details-control',
            "orderable": false,
            "data": null,
            "defaultContent": ''
          },
          { mData: 'LogEntryType' },
          { mData: 'PokemonName' },
          { mData: 'XpReward' },
          { mData: 'StardustReward' },
          { mData: 'Iv' },
          { mData: 'Shiny' },
          { mData: 'timestamp' }
        ],
        "order": [[ 7, "desc" ]],
        "columnDefs": [
            {
              "render": function ( data, type, row ) {
                if (row.PokemonName == "Missingno") {
                  return "-";
                }
                return data +"%";
              },
              "targets": 5
            },
            {
              "render": function ( data, type, row ) {
                if (row.PokemonName == "Missingno") {
                  return "-";
                }
                return data;
              },
              "targets": 2
            },
            {
              "render": function ( data, type, row ) {
                return moment.utc(data).local().format('DD/MM/YYYY HH:mm:ss');
              },
              "targets": 7
            }
        ],
        "fnDrawCallback": function( oSettings ) {
          // Add event listener for opening and closing details
          $('#logEntries tbody').off().on('click', 'td.details-control', function () {
              var tr = $(this).closest('tr');
              var row = table.row( tr );

              if (row.data().PokemonName == "Missingno"){
                return;
              }

              if ( row.child.isShown() ) {
                  // This row is already open - close it
                  row.child.hide();
                  tr.removeClass('shown');
              }
              else {
                  // Open this row
                  row.child( format(row.data()) ).show();
                  tr.addClass('shown');
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
    });
    
    function autoRefresh(){
      var table = $('#logEntries').DataTable();
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
	<?php 
	if (isset($_GET["id"])) {
		echo '<h2>Session '.$_GET["id"].' for <a href="account.php?id='.$data["Id"].'">'.hideAccountName($data["Name"]).'</a></h2><br />';
	} else {
		echo '<h2>Day '.$_GET["date"].' for <a href="account.php?id='.$data["Id"].'">'.hideAccountName($data["Name"]).'</a></h2><br />';
	}
	?>
	<div id="vis-buttons">
        Toggle column: 	<button type="button" class="btn btn-outline-secondary" data-column="1">Type</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="2">Pokemon Name</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="3">Xp Reward</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="4">Stardust Reward</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="5">IV</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="6">Shiny</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="7">Timestamp</button>
    </div><br />
      <table id="logEntries" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th ></th>
            <th >Type</th>
            <th >Pokemon Name</th>
            <th >Xp Reward</th>
            <th style="white-space:normal;">Stardust Reward</th>
            <th >IV</th>
            <th >Shiny</th>
            <th >Timestamp</th>
          </tr>
        </thead>
      </table>
	</div>
	<a href="https://paypal.me/pools/c/8nDB1mCCQz"><img src="images/paypal_donate.jpg" style="width:300px;height:75px;"/></a>
	</body>
</html>