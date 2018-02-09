$(function() {
    var fromDate = $("#expire").datepicker({
		dateFormat: "yy-mm-dd",
        defaultDate: "+1d",
        changeMonth: true,
        numberOfMonths: 1,
        minDate: new Date(),
        onSelect: function(selectedDate) {
            var instance = $(this).data("datepicker");
            var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
            date.setDate(date.getDate()+1);
            toDate.datepicker("option", "minDate", date);
        }
    });
    
    
});