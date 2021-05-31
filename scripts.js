// progress bar script
      //var progress = document.getElementById("progress1");
      //progress.onclick=function() {addprogress()};     
     //document.getElementById("progress1").addEventListener("click", addprogress);
     function addprogress(progress) {
        var progress = document.getElementById("progress"); //returning id of each input
        var myprogressbar = document.getElementById("progressbar"); //returning id of progress bar
        var myprogressbartext = myprogressbar.textContent;
        var widthprogress = myprogressbar.style.width ; //returning the value of width as a string
        // error -- empty -- no string returning
        var widthprogressint= parseInt( widthprogress,10) ;// value of width as an integer -- error : value is nan
        //document.write( progressvalue); // testing values 
        var mywidth = 10;
        //var progressvalue = document.getElementById(progress).value;
        //document.write(widthprogressint);
        //if (progressvalue == !"") {
        widthprogressint += mywidth ;// adding 10 to value of width
        myprogressbar.style.width = widthprogressint + '%' ; //output of new value of width
        myprogressbar.textContent =  widthprogressint + '%' ; // output of the text 
        //}
        
        
      }
      //stupid code but it works (kichghol)
      /*function addprogress1(progress) {
        var progress = document.getElementById("progress1");
        //document.getElementById("progress1").onclick= addprogress ;
        var myprogressbar = document.getElementById("progressbar");
        //var progressbar_width = myprogressbar.style.width;
        var mywidth = 10 ;
        //for (let i = 0; i < j; i++) {
                
        mywidth += mywidth;
        //}       
        myprogressbar.style.width = mywidth + '%' ;
        
         
         
      }
      function addprogress2(progress) {
        var progress = document.getElementById("progress2");
        //document.getElementById("progress1").onclick= addprogress ;
        var myprogressbar = document.getElementById("progressbar");
        //var progressbar_width = myprogressbar.style.width;
        var mywidth = 20 ;
        //for (let i = 0; i < j; i++) {
                
        mywidth += mywidth;
        //}
               
        //while (myprogressbar.style.width<=100) {
        myprogressbar.style.width = mywidth + '%' ;
        //}
         
         
      }
      function addprogress3(progress) {
        var progress = document.getElementById("progress3");
        //document.getElementById("progress1").onclick= addprogress ;
        var myprogressbar = document.getElementById("progressbar");
        //var progressbar_width = myprogressbar.style.width;
        var mywidth = 30 ;
        //for (let i = 0; i < j; i++) {
                
        mywidth += mywidth;
        //}
               
        //while (myprogressbar.style.width<=100) {
        myprogressbar.style.width = mywidth + '%' ;
        //}
         
         
      }
      function addprogress4(progress) {
        var progress = document.getElementById("progress4");
        //document.getElementById("progress1").onclick= addprogress ;
        var myprogressbar = document.getElementById("progressbar");
        //var progressbar_width = myprogressbar.style.width;
        var mywidth = 40 ;
        //for (let i = 0; i < j; i++) {
                
        mywidth += mywidth;
        //}
               
        //while (myprogressbar.style.width<=100) {
        myprogressbar.style.width = mywidth + '%' ;
        //}
         
         
      }*/
        //var progress = document.getElementById(progress);
        //progress.forEach(addprogress);
      function move() {
        var elem = document.getElementById("myBar");
        var width = 1;
        var id = setInterval(frame, 10);
        function frame() {
          if (width >= 100) {
            clearInterval(id);
          } else {
            width++;
            elem.style.width = width + '%';
          }
        }
      }
      //
        function addinput() {
            var myform = document.getElementById("myform");
            var myinput = document.createElement("input");
            myinput.type = "text";
            myinput.className = "form-control";
            myform.appendChild(myinput);
        }
        function addtextarea() {
            var myform = document.getElementById("myform");
            var mytextarea = document.createElement("textarea");
            mytextarea.rows = "5";
            mytextarea.className = "form-control";
            myform.appendChild(mytextarea);
        }