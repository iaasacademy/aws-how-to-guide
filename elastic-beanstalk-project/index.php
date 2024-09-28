<?php include("server.php"); ?>

<?php $result = mysqli_query($db, "SELECT * FROM user order by id desc");


if (isset($_POST['search'])) {

   $search_term = $_POST['ftask_type'];

   $fstatues= $_POST['fstatues'];
//var_dump( $fstatues);
  // $sql .= "WHERE task_type = '{$search_term}' ";

   $result = mysqli_query($db, "SELECT * FROM user WHERE task_type = $search_term AND  statues= $fstatues  order by id desc");
}
//header("Location: index.php/#tabels");

?>
<?php 
//Edit Todo Selected Id
	if (isset($_GET['edit'])) {
		$id = $_GET['edit'];
		$update = true;
		$record = mysqli_query($db, "SELECT * FROM user WHERE id=$id " );

		if (count(array($record)) == 1 ) {
			$n = mysqli_fetch_array($record);
			$task_type = $n['task_type'];
			$task_description = $n['task_description'];
         $status = $n['status'];
         $due_date = $n['due_date'];
		}
	}
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
  <title>Task Schedule</title>


  <link href='https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>


	<style>
		body {
    font-family: 'Montserrat';
}
#tabels select{
   color: #7a7685!important;
   font-size:18px;
}

#user_form{
   padding: 7%;
    border-radius: 15px;
}

table.dataTable thead th, table.dataTable thead td{
   border-bottom: 0px!important;

}

table thead {
   border-bottom: 0px!important;
   border-radius: 10px!important;
}

@media(min-width:300px) and (max-width:962px){
   .textb{
      padding:0px!important;
   }
   #date,#time
   {
      font-size: 26px!important;
   }
   .times
   {
      font-size: 35px!important;
   }
}
/* .table tbody tr:nth-of-type(even) {
    background-color: rgb(171 189 207 / 42%)!important;
}

.table tbody tr:nth-of-type(od) {
    background-color: #fff!important;
} */
 .maintxt {
    /* background: linear-gradient(rgba(0,0,0,.3), rgba(0,0,0,.3)), url(images/top-bg.jpg); */
    background:url(images/top-bg.jpg);
    background-size: cover;
    -webkit-background-size: cover;
     -moz-background-size: cover;
     -o-background-size: cover;
     background-size: cover;
     height: 450px;
     color: #fff;
    /* text-shadow:3px 3px 20px #000, 3px 3px 20px #000; */
     padding-top: 60px;
}
.form-group{
   margin-bottom:25px;
   margin-top:25px;
}


.bg-overlay {
    background: linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.7)), url("images/bg1.jpg");
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center center;
    color: #fff;
    height: 450px;
    padding-top: 50px;
}

.bg-overlay1 {
    background:url("images/footer-image.jpg");
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center center;
    color: #fff;
    height: 450px;
    padding-top: 50px;
}
.textb
{
   text-align:center;
   margin-top:5%;
   padding:30px;
   width: 100%;
  
}
thead th{
   color:#fff;
   font-weight:bold;
}
.bt{
   width:200px;
   height:50px;
   font-weight:bold;
}
td .btn{
    width:80px; 
   height:30px;
}
.form-control {
   font-weight:bold;
   font-size:18px!important;
}

::placeholder,select {

    font-weight: bolder;
    color:#fff!important;
   
}

.btn1s{
     background:#fdb044;
    margin-top: 7px;
}
.btn2s{
     background:#a5d6fe;
      margin-top: 7px;
}

.btn3s{
     background:#76dc76;
      margin-top: 7px;
}

.btn4s{
     background:#fe422f;
      margin-top: 7px;
}

.btn5s{
     background:#bcc2c4;
}




p.input-container {
  width: 100%;
  position: relative;
  top: 50px;
  margin-bottom: 25px;
}
#user_form label {
  color: #000;
  position: relative;
  font-size:18px;
  font-weight:bolder;
  cursor: text;
  transform: translateY(-25px);
  transition: transform 0.3s ease;
  left: -30%;
  bottom: 5px;
}
#user_form input {
  width: 100%;
  height: 40px;
  /* font-size: 16px; */
  color:#fff;
  transition: 0.6s;
  border: none;
  border-bottom: 2px solid #fff;
  background-color: transparent;
}
input:focus {
  outline: none;
  border-bottom: 2px solid #2f7c9a;
}
select:focus {
  outline: none!important;
  border-bottom: 2px solid #2f7c9a;
}
.animation label {
  transform: translateY(-55px);
  font-size: 10px;
  text-transform: uppercase;
  font-weight: 600;
}
.animation-color label {
  color: #2f7c9a;
}

#user_form .form-control{
   border: none;
  border-bottom: 2px solid #fff;
  background-color: transparent;
   border-radius:0px;
}
.error{
   float: left;
    margin-top: 3px;
    color: red;
}

.table:first-child{
   border: 1px solid #2f7c9a;
   margin-bottom:0px;
}
.sliderh1{
    font-size:30px;
}
.times{
  font-size:25px;
  font-weight:900;
  line-height:50px;
}
.ts1,.ts tr
{
   border: 1px solid #2f7c9a; 
}
.ts1{
   border-radius: 1em;
    overflow: hidden;

}
.dataTable 
{
   border-radius: .5em;
  overflow: hidden;
}

.ts1 th{
   background: #2f7c9a;
 
}

#user_form select option {
  margin: 40px;
  background: #fff;
  color: #000;
  text-shadow: 0 1px 0 rgba(0, 0, 0, 0.4);
}
.dataTable th
{
   padding: 14px;
   text-align:center;

}

.dataTables_wrapper .dataTables_filter input{
   background:#fff!important;
}


	</style>
</head>
<body>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@500&display=swap" rel="stylesheet">

<div class="container-fluid maintxt">
	 <div class="row text-start ">
         <div class="col-lg-3 "></div>
		  <div class="textb col-lg-6">
        <h2  class="sliderh1" style="line-height:45px;color:#262427;font-family: Raleway"><b>“Deciding What NOT To Do Is As Important <br> As Deciding What To Do” </b></h2>
                                                                    <p style="margin-left:58%;font-size:22px;" >     <i style="color: #262427;font-family: Raleway">–- Steve Jobs --</i> </P>
        <!-- <h3 class="" style="line-height:45px; text-align: center; ">Get focused, organized, and calm with our ultimate To-Do List. 
</h3>-->
        </div>
        <div class="col-lg-3"></div>
	</div> 
</div>

	<!-- Alert jika CRUD data -->
	

	<!-- Form CRUD data -->

   <div class="container text-center" >
      <!-- <h1 class="text-center" style="margin:20px;">Live Time is:10/11/2022 - 5:30:20 am</h1> -->
      <div class="row" style="    background: url(images/todo.png);
    background-repeat: no-repeat;
    background-size: 23%;
    background-position: right;">
      <div class="col-lg-4"><image src="images/timeanddate.jpg" class="image-responsive" style="max-width:300px;"> </div>
      <div class="col-lg-8" style="text-align:start;
">
         <h1 class="times" style="color:#c65b16;font-size:34px;font-weight:600;font-family: Raleway">Get focused, organized, and <br> calm with our ultimate <a style="color:#000">To-Do List.</a></h1>


         <table class="table ts ts1" style="width:60%;border-collapse:initial">
  <tr style="  border-radius: 15px 50px 30px 5px;">
    <th style="font-size:20px;color:#fff;text-align:center">Date</th>
    <th style="font-size:20px;color:#fff;text-align:center">Time</th>
  </tr>
  <tr>
    <td style="border-right:1px solid #c9c7c7">  <h4 id="date"   class="text-center" style="margin:20px;font-size:30px;font-weight:600;font-family: Times New Roman;"></h4>
</td>
    <td>  <h4 id="time"   class="text-center" style="margin:20px;font-size:30px;font-weight:600;font-family: Times New Roman;"></h4>
</td>
  </tr>

</table>

    
      </div>
      </div>
      
<hr>
<script type="text/javascript">
var tmonth=["jan","feb","march","April","May","June","July","August","September","October","November","December"];
var tmonth=["1","2","3","4","5","6","7","8","9","10","11","12"];

function GetClock(){
var d=new Date();


var times = d.toLocaleString();
var nmonth=d.getMonth(),ndate=d.getDate(),nyear=d.getFullYear();

var nhour=d.getHours(),nmin=d.getMinutes(),nsec=d.getSeconds();
if(nmin<=9) nmin="0"+nmin;
if(nsec<=9) nsec="0"+nsec;

var clocktext=""+times+"";
document.getElementById('date').innerHTML= d.getDate()+'/'+tmonth[d.getMonth()]+'/'+d.getFullYear(); 
document.getElementById('time').innerHTML= d.toLocaleTimeString();
}

GetClock();
setInterval(GetClock,1000);
</script>


<br>

<h1  class="text-center" style="margin:20px;;font-weight:600">My To Do List</h1>

<form id="user_form" class=""  style="background:#2f7c9a" >
      <div class="row " >
      <input type="hidden" value="1" name="type">
		   <!-- <input type="hidden" name="id" value="<?php echo $id; ?>"> -->
         <div class="col-lg-6  col-md-6" >
            <div class="form-group">
            <select class="form-control"  id="task_type" required name="task_type">
  <option value="">Task Type</option>
  <option value="1">Finance</option>
  <option value="2">Work </option>
  <option value="3">Family</option>
  <option value="4">Health</option>
  <option value="5">Other</option>
</select>

         <!-- <input type="text"  class="form-control" placeholder="Task type" value="<?php echo $task_type; ?>"> -->
      </div>
       </div>
       <div class="col-lg-6  col-md-6" >
            <div class="form-group input-container">
               <input type="text" id="task_description"  name="task_description" maxlength="500" class="todo-input form-control" placeholder="Task Description" value="<?php echo $task_description; ?>">
               <!-- <label for="todo-input" unselectable="on">Task Description</label> -->
            </div>
   </div>

   <div class="col-lg-6  col-md-6" >
            <div class="form-group">
               <input type="text" value="" id="txtDate" onfocus="(this.type='date')" name="due_date" class="form-control" placeholder="Due Date" value="<?php echo $due_date; ?>">
            
            </div>
   </div> 
   
   
   <div class="col-lg-6  col-md-6" >
            <div class="form-group">
            <select class="form-control" id="statues" required name="statues">
  <option  value="" >Status</option>
  <option value="1">Started</option>
  <option value="2">In-Progress </option>
  <option value="3">Completed</option>
  <option value="4">Delayed</option>
  <option value="5">Not started</option>
</select>

         <!-- <input type="text"  class="form-control" placeholder="Task type" value="<?php echo $task_type; ?>"> -->
      </div>
</div>

       <br>
       <div class="col-lg-12 col-sm-12 text-center" style="padding-top:40px;">
      <?php if ($update == true): ?>
		   <input type="submit" name="update" value="Update" class="form-control bt btn-sm" style="background-color: #2f7c9a;color:#fff">
		<?php else: ?>
		<button type="button"  id="add-user"  class=" btn form-control bt btn-sm" style="background-color: #fff;color: #2f7c9a;;border-radius:10px;">SUBMIT</button>
		<?php endif ?>
      </div>
      </div>
	</form>
      </div>
<br><br>
	<!-- Table data -->
   <div class="container-fluid ftable" id="tabels"  style="background:#e5f5fb; padding-top: 50px;" >
   <div class="container text-center" >

   <?php if (isset($_SESSION['alert'])) : ?>
	<h2>
		<?php
			echo $_SESSION['alert'];
			unset($_SESSION['alert']);
		?>
	</h2>
	<?php endif ?>

<div class="table-" >
   <div class="row">
      <form action="#tabels" method="post" style="width:80%">
      <div class="row " >
<div class="col-lg-4  col-md-4" >
            <div class="form-group">
            <select class="form-control"  id="ftask_type" required name="ftask_type">
  <option value="">Task Type</option>
  <option value="1">Finance</option>
  <option value="2">Work </option>
  <option value="3">Family</option>
  <option value="4">Health</option>
  <option value="5">Other</option>
</select>

         <!-- <input type="text"  class="form-control" placeholder="Task type" value="<?php echo $task_type; ?>"> -->
      </div>
       </div>

      <div class="col-lg-4   col-md-4" >
      <div class="form-group">
         <select class="form-control" id="fstatues" required name="fstatues">
            <option value="" >Status</option>
            <option value="1">Started</option>
            <option value="2">In-Progress </option>
            <option value="3">Completed</option>
            <option value="4">Delayed</option>
            <option value="5">Not started</option>
         </select>
      </div>
      </div>
      <div class="col-lg-4  col-md-4" >
      <div class="form-group">

      <input type="submit" name="search" class="filter btn btn-primary" style="background-color: #7a7685!important;border:1px solid #7a7685"  value="SEARCH">
      <a href="index.php" class="btn btn-info" >REFERESH</a>
      </div>   
      </form>
         </div>
         </div>
         </div>
      </div>
      <div class="table-responsive"> 
	<table  class="table display dashed   responsive nowrap ts dataTable" id='todoTable'>
		<thead style="background-color:#7a7685;border-radius:10px;height: 50px;
    line-height: 26px;">
      <th>#</th>
			<th>Task Type</th>
			<th>Task Description</th>
			<th> Date</th>
         <th>Status</th>
         <th></th>
		</thead>
      <tbody>
		<?php while ($row = mysqli_fetch_array($result)) { ?>
		<tr>
         <td style="text-align:center;line-height: 40px;"><?php echo $row['id'] ?></td>
			<td style="font-size:24px!important;line-height: 40px;">
            
            
            <?php 
         
         if($row['task_type'] == 1 ) 
         {
         
         echo 'Finance';

         }


         elseif($row['task_type'] == 2 ) 
         {
         
         echo 'Work';

         }

         elseif($row['task_type'] == 3 ) 
         {
         
         echo 'Family';

         }

         elseif($row['task_type'] == 4 ) 
         {
         
         echo 'Health';

         }

         else 
         {
         
         echo 'Other';

         }
         
         
         ?></td>
			<td style="word-break:break-all;line-height: 40px;max-width:350px;font-size:18px!important;"><?php echo $row['task_description']; ?> </td>
         <td style="font-size:18px!important;font-weight:bolder;line-height: 40px;"> 
      <?php 
      
      $originalDate = $row['due_date'];;
$newDate = date("d-m-Y", strtotime($originalDate));
      
      echo  $newDate ?>
            
         </td>

         <td>
            
         <select class=" form-control" id="selectchange" required name="statues" style="min-width:150px;">
  <option >Status</option>
  <option value="1" data-id="<?php echo $row['id']; ?>"  <?php  if($row['statues'] == 1 ) echo 'selected' ?>>Started</option>
  <option value="2" data-id="<?php echo $row['id']; ?>"  <?php  if($row['statues'] == 2 ) echo 'selected' ?> >In-Progress </option>
  <option value="3" data-id="<?php echo $row['id']; ?>"  <?php  if($row['statues'] == 3 ) echo 'selected' ?> >Completed</option>
  <option value="4" data-id="<?php echo $row['id']; ?>"  <?php  if($row['statues'] == 4 ) echo 'selected' ?>>Delayed</option>
  <option value="5" data-id="<?php echo $row['id']; ?>"  <?php  if($row['statues'] == 5 ) echo 'selected' ?>>Not started</option>
</select>
<!-- </td
            <?php 
         
         if($row['statues'] == 1 ) 
         {
         
         echo 'Started';

         }


         elseif($row['statues'] == 2 ) 
         {
         
         echo 'In-Progress';

         }

         elseif($row['statues'] == 3 ) 
         {
         
         echo 'Completed';

         }

         elseif($row['statues'] == 4 ) 
         {
         
         echo 'Delayed';

         }

         else 
         {
         
         echo 'Not started';

         }

       
         ?> -->
         </td>
         <td>
         <?php  if($row['statues'] == 1 ) 
         {
         
         echo '<button class="btn btn1s" style="line-height: 40px;" ></button>';

         }


         elseif($row['statues'] == 2 ) 
         {
         
            echo '<button class="btn btn2s" style="line-height: 40px;" ></button>';

         }

         elseif($row['statues'] == 3 ) 
         {
         
            echo '<button class="btn btn3s" style="line-height: 40px;" ></button>';

         }


         elseif($row['statues'] == 4 ) 
         {
         
            echo '<button class="btn btn4s"  style="line-height: 40px;"></button>';

         }


         else 
         {
         
        // echo 'Completed';

         echo '<button class="btn btn5s" ></button>';

         }

         ?>
         </td>


        

		
		</tr>
		<?php } ?>
      <tbody>
	</table>
   </div>
      </div>
   <br><br>
      </div>
      </div>



      <div class="container-fluid bg-overlay1">
	<div class="row text-start textb">
		
        <!-- <h3 class="" style="line-height:45px;">While technology contributes to economic <br> and human prosperity, it can also  negative impacts <br> like pollution or resource</h3> -->
     
        <!-- <button type="button" class="btn btn-primary btn-lg">Get Started</button> -->
	</div>
</div>

<script>
   $(function(){
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();
    
    var maxDate = year + '-' + month + '-' + day;

    // or instead:
    // var maxDate = dtToday.toISOString().substr(0, 10);

    //alert(maxDate);
    $('#txtDate').attr('min', maxDate);
});






//Load animation if fields containing data on page load
$( document ).ready(function() {
  $("todo-input").each(function() { 
    if ($(this).val() != "") {
      $(this).parent().addClass("animation");
    }
  });
});

//Add animation when input is focused
$(".todo-input").focus(function(){
  $(this).parent().addClass("animation animation-color");
});

//Remove animation(s) when input is no longer focused
$(".todo-input").focusout(function(){
  if($(this).val() === "")
    $(this).parent().removeClass("animation");
  $(this).parent().removeClass("animation-color");
})

</script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->

<!-- jQuery Library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Datatable JS -->
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>


<script>
 //  $('.table').hide();
//$('#tabels').hide();
$(document).on('click','#add-user',function(e) {
		var data = $("#user_form").serialize();
    //  var task_type = $('#task_type').val();
     // alert(task_type);
     var task_description = $('#task_description').val();
    // var statues = $('#statues').val();
     var due_date = $('#due_date').val();
     //alert(due_date);
      if (task_description == '' ) {  
      $('#task_description').after('<span class="error">Task Description is required</span>'); 
      exit; 
    } 

    if( $("#task_type option:selected").val()=='') {  
      $('#task_type').after('<span class="error">Task Type is required</span>'); 
      exit; 
    } 

    if( $("#statues option:selected").val()=='') {  
      $('#statues').after('<span class="error">Status  is required</span>'); 
      exit; 
    } 

    if (due_date == '') {  
      $('#due_date').after('<span class="error">Due date is required</span>'); 
      exit; 
    } 

   //  else( $("#statues option:selected").val()=='' ||  $("#task_type option:selected").val()==''  )
   //  {
   //    return false;
   //  }

    

    //  alert(data);
		$.ajax({
			data: data,
			type: "POST",
         dataType: 'json',
			url: "server.php",
			success: function(dataResult){
				//alert(dataResult);
					if(dataResult.statusCode==200){
						//$('#addEmployeeModal').modal('hide');
						alert('To Do added successfully !'); 
                  $('#user_form')[0].reset();
             //     $('#tabels').show();
                       location.reload();	
//                       setTimeout(function(){
//    $( ".table" ).load( "index.php .table" );
// }, 100);					

					}
					else if(dataResult.statusCode==201){
					   alert(dataResult);
					}
			}
		});
	});


//    $(document).ready(function(){
//   // save comment to database
//   $(document).on('click', '#add-user', function(){
//     var task_type = $('#task_type').val();
//     var task_description = $('#task_description').val();
//     var statues = $('#statues').val();
//     var due_date = $('#due_date').val();
//     $.ajax({
//       url: 'server.php',
//       type: 'POST',
//       data: {
//         'save': 1,
//         'task_type': task_type,
//         'task_description': task_description,
//         'statues': statues,
//         'due_date': due_date,
//       },
//       success: function(response){
//        // $('#task_type').val('');
//        // $('#task_description').val('');
//        alert('hai');
//        // $('#display_area').append(response);
//       }
//     });
//   });
// });


        $(document).ready(function(){
      //   $("#tabels").hide();
       //  $(".table").hide();

            $(document).on("change","#selectchange",function(e){
                var statues1 = $(this).val();
               // alert(status);
                var ids = $(this).find(':selected').attr('data-id');
               // alert(ids);
               //  if(statues == 1){
               //    statues = 0;
               //  }else{
               //    statues = 1;
               //  }
                $.ajax({
                    url : "server.php",
                    type : "POST",
                    dataType: 'json',
                    data : {statues1:statues1,ids:ids},
                    success : function(response){
                     // alert(response);
                      //$("#tabels").load("table");
                    //  $("#tabels").show();
//                       setTimeout(function(){
//    $( ".table" ).load( "index.php .table" );
// }, 100);
                       location.reload();	

                  //   $(".table").show();
                     // window.location.href = "index.php";
                    }
                });
            });
        });


         $(document).ready(function () {
     $('#todoTable').DataTable();
 });





    </script>



</body>
</html>