<!-- JAVASCRIPT -->
<script src="{{ asset('assets/js/sidebar.js') }}"></script>
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/scroll-top.init.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>

<!-- <script src="{{ asset('assets/libs/air-datepicker/air-datepicker.js') }}"></script> -->
<!-- <script src="{{ asset('assets/js/plugins/air-datepicker.init.js') }}"></script> -->


<script>
// window.datepickerLocaleID = {
//     days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
//     daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
//     daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
//     months: [
//         'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
//         'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
//     ],
//     monthsShort: [
//         'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
//         'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
//     ],
//     today: 'Hari ini',
//     clear: 'Hapus',
//     dateFormat: 'dd/MM/yyyy',
//     timeFormat: 'hh:ii aa',
//     firstDay: 1
// };
</script>

<script>
      function isNumberKey(evt){
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
          && (charCode < 48 || charCode > 57))
          return false;
          return true;
      }  
      function isNumberKey2(evt){
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 37 && charCode != 46 && charCode > 31 
          && (charCode < 48 || charCode > 57))
          return false;
          return true;
      }  

      function filterFloat(value) {
        value = value.replace("%", "");
        if (isNaN(value) || value==="") {
          return 0;
        }
        return parseFloat(value);
      }

      $("input[data-type='currency']").on({
          keyup: function() {
            formatCurrency($(this));
          },
          blur: function() { 
            formatCurrency($(this), "blur");
          }
      });


      function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      }


      function formatCurrency(input, blur) {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.
        
        // get input value
        var input_val = input.val();
        
        // don't validate empty input
        if (input_val === "") { return; }
        
        // original length
        var original_len = input_val.length;

        // initial caret position 
        var caret_pos = input.prop("selectionStart");
          
        // check for decimal
        if (input_val.indexOf(".") >= 0) {

          // get position of first decimal
          // this prevents multiple decimals from
          // being entered
          var decimal_pos = input_val.indexOf(".");

          // split number by decimal point
          var left_side = input_val.substring(0, decimal_pos);
          var right_side = input_val.substring(decimal_pos);

          // add commas to left side of number
          left_side = formatNumber(left_side);

          // validate right side
          right_side = formatNumber(right_side);
          
          // On blur make sure 2 numbers after decimal
          if (blur === "blur") {
            //right_side += "00";
          }
          
          // Limit decimal to only 2 digits
          right_side = right_side.substring(0, 2);

          // join number by .
          //input_val = "$" + left_side + "." + right_side;
          //input_val = left_side + "." + right_side;
          input_val = left_side;

        } else {
          // no decimal entered
          // add commas to number
          // remove all non-digits
          input_val = formatNumber(input_val);
          //input_val = "$" + input_val;
          input_val = input_val;
          
          if (blur === "blur") {
            //input_val += ".00";
          }
        }
        
        input.val(input_val);

        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
      }  

</script>
<script>
// document.addEventListener('DOMContentLoaded', function () {

//   function initAirDatepicker(input) {
//     if (input._airdatepicker) return;

//     // initialize
//     const dp = new AirDatepicker(input, {
//       appendTo: document.body,
//       zIndex: 3000,
//       locale: window.datepickerLocaleID,
//       autoClose: true,
//       dateFormat: 'dd/MM/yyyy',
//       selectMonth: true,
//       selectYear: true,
//       position: 'bottom center',
//       buttons: ['today', 'clear'],
//       autoSelect: true,
//     });

//     input._airdatepicker = dp;

//     // 🔹 When value is changed manually or via jQuery
//     input.addEventListener('change', function () {
//       const val = this.value?.trim();
//       if (val) {
//         // parse the value safely
//         const date = AirDatepicker.parseDate(val, 'dd/MM/yyyy');
//         if (date instanceof Date && !isNaN(date)) {
//           dp.selectDate(date); // highlight the date
//         }
//       } else {
//         dp.clear();
//       }
//     });
//   }

//   // initialize outside modals
//   document.querySelectorAll('.air-datepicker-input').forEach(input => {
//     if (!input.closest('.modal')) initAirDatepicker(input);
//   });

//   // initialize inside modals
//   document.querySelectorAll('.modal').forEach(modal => {
//     modal.addEventListener('shown.bs.modal', function () {
//       modal.querySelectorAll('.air-datepicker-input').forEach(input => {
//         initAirDatepicker(input);
//       });
//     });

//     modal.addEventListener('hidden.bs.modal', function () {
//       modal.querySelectorAll('.air-datepicker-input').forEach(input => {
//         if (input._airdatepicker) {
//           input._airdatepicker.destroy();
//           input._airdatepicker = null;
//         }
//       });
//     });
//   });

// });

</script>
