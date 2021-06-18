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
		
      var table = $('#sessions').DataTable({
        "processing": true,
        "ajax": {
          "url": 'ajax/account_pokemon.php',
          "data":function(d) {
			return  $.extend(d, myData);
          }
        },
        "search": {
          "regex": true
        },
        "aoColumns": [
          { mData: 'Pokemon' },
          { mData: 'Caught' },
          { mData: 'Fleet' },
          { mData: 'Shiny' },
          { mData: 'ShinyRate' },
          { mData: 'Candy' }
        ],
        "columnDefs": [
            {
              "render": function ( data, type, row ) {
                return data +"%";
              },
              "targets": 3
            }
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
    <h2><a href="account.php?id=<?php echo $_GET["id"] ?>"><?php echo (isset($data) ? hideAccountName($data["Name"]) : "AccountName not Found") ?></a></h2>
		
	<?php 
		if(isset($data)){
			echo "<strong>Level:</strong> " . $data["Level"] . " <strong>Exp:</strong> " . $data["Experience"] . "/" . $data["NextLevelExp"] . " <strong>Stardust:</strong> " . $data["Stardust"] . " <strong>Pokecoins:</strong> " . $data["Pokecoins"] . " <strong>Team:</strong> " . $data["Team"] . "<br />";
		}
	?>
    <br />
	<div id="vis-buttons">
        Toggle column: 	<button type="button" class="btn btn-outline-secondary" data-column="0">Pokemon Name</button> 
						<button type="button" class="btn btn-outline-secondary" data-column="1">Caught</button>
						<button type="button" class="btn btn-outline-secondary" data-column="2">Fleet</button>
						<button type="button" class="btn btn-outline-secondary" data-column="3">Shiny</button>
						<button type="button" class="btn btn-outline-secondary" data-column="4">Shiny Rate</button>
						<button type="button" class="btn btn-outline-secondary" data-column="5">Candy</button>
    </div><br />
	<div id="">
        Switch to: <button type="button" class="btn btn-outline-secondary" onclick="location.href = 'account.php?id=<?php echo $_GET["id"] ?>';">Session</button>
    </div><br />
      <table id="sessions" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th >Pokemon Name</th>
            <th >Caught</th>
            <th >Fleet</th>
            <th >Shiny</th>
            <th >Shiny Rate</th>
            <th >Candy</th>
          </tr>
        </thead>
      </table>
	</div>
	<a href="https://paypal.me/pools/c/8nDB1mCCQz"><img src="images/paypal_donate.jpg" style="width:300px;height:75px;"/></a>
	</body>
</html>