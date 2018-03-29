require([
        'jquery',
        'Magento_Ui/js/modal/modal',
        ],
        function($,modal) {
            setTimeout(function() {
            var options = {
                type: 'popup',responsive: true,innerScroll: true,title:''
            };
            var popup = modal(options, $('#jute_popup'));
                $('#jute_popup').modal('openModal');
                $("#jute_popup").css('display','block');
                }, 1000);
      });
