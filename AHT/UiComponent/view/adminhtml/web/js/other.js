require(['jquery', 'domReady!'], function($) {
    $(document).ready(function() {
        var waitForEl = function(selector, callback){
            if(jQuery(selector).length){
                callback();
            }else{
                setTimeout(function(){
                    waitForEl(selector, callback);
                },100);
            }
        }

        var selector = '[name="customer[company_type]]';
        waitForEl(selector , function(){
            if($('[name="customer[company-type]"]').val() == 4){
                $('[data-index="organization-name"]').show();
            }else{
                $('[data-index="organization-name"]').hide();
            }
        })

        $('[name="customer[company-type]"]').change(function(){
            var data = $(this).val();
            if(data == 4){
                $('[data-index="organization-name"]').hide();
                $('[name="customer[company-type]"]').val('');
            }   
        })
    });
});