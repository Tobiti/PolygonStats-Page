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
	<script type="text/javascript" language="javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-moment.js"></script>
	<script type="text/javascript" language="javascript" class="init">
	
    /* Formatting function for row details - modify as you need */
    function format ( d ) {
        // `d` is the original data object for the row
        return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
          '<tr>'+
            '<td><a href="http://pokemondb.net/pokedex/'+ d.PokemonName.toLowerCase().replace("male", "-m").replace("female", "-f") +'"><img src="https://img.pokemondb.net/sprites/bank/normal/'+ d.PokemonName.toLowerCase().replace("male", "-m").replace("female", "-f") +'.png" alt="'+d.PokemonName+'"></a></td>'+
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
      var table = $('#logEntries').DataTable({
        "processing": true,
        "ajax": {
          "url": 'ajax/session_log_entries.php',
          "data": {
            "session_id": "<?php echo $_GET["id"] ?>"
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
          { mData: 'XpReward' },
          { mData: 'StardustReward' },
          { mData: 'Iv' },
          { mData: 'Shiny' },
          { mData: 'timestamp' }
        ],
        "order": [[ 6, "desc" ]],
        "columnDefs": [
            {
              "render": function ( data, type, row ) {
                if (row.PokemonName == "Missingno") {
                  return "-";
                }
                return data +"%";
              },
              "targets": 4
            },
            {
              "render": function ( data, type, row ) {
                return moment.utc(data).local().format('DD/MM/YYYY HH:mm:ss');
              },
              "targets": 6
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
    <h2>Session #<?php echo $_GET["id"] ?> for <?php echo $_GET["account"] ?></h2>
      <table id="logEntries" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th ></th>
            <th >Type</th>
            <th >Xp Reward</th>
            <th >Stardust Reward</th>
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