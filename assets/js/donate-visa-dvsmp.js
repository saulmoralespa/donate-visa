(function($){
    $(function(){

        let donateVisaNameLast;
        let donateVisaCompany;
        let donateVisaEmail;
        let donateVisaPrice;

        $("form#donate_visa-frontend").trigger("reset");

        $("input[name='donate-visa-price']").change( function() {

            let donateVisaPrice = $('#donate-visa-price').val();

            V.init( {
                apikey: donatevisadvsmp.apikey,
                paymentRequest:{
                    currencyCode: donatevisadvsmp.currency,
                    total: donateVisaPrice
                },
                settings: {
                    locale: donatevisadvsmp.locale
                },
                review: {
                    buttonAction: "Pay"
                }
            });

            V.on("payment.success", function(payment)
            {

                let currencyCode = payment.vInitRequest.paymentRequest.currencyCode;

                $.ajax({
                    type: 'POST',
                    url:  donatevisadvsmp.ajaxurl,
                    data: {action: 'donate_visa_dvsmp', currencyCode: currencyCode, price: donateVisaPrice, name: donateVisaNameLast, company: donateVisaCompany, email: donateVisaEmail},
                    beforeSend: function(){
                        $('.v-checkout-wrapper').hide();
                    },
                    success: function(r){
                        $(".donate-visa-alert").append("<span style='color:green;font-size:18px'>"+donatevisadvsmp.successMsj+"<br></span>");
                    }
                });

            });
            V.on("payment.cancel", function(payment)
            {
                $('.v-checkout-wrapper').hide();
                $('button[type="submit"]').prop('disabled', true);
                $(".donate-visa-alert").append("<span style='color:orange;font-size:18px'>"+donatevisadvsmp.cancelMsj+"<br></span>");
            });
            V.on("payment.error", function(payment, error)
            {
                $('.v-checkout-wrapper').hide();
                $('button[type="submit"]').prop('disabled', true);
                $(".donate-visa-alert").append("<span style='color:red;font-size:18px'>"+donatevisadvsmp.errorMsj+"<br></span>");
            });

        });



        $('form#donate_visa-frontend').submit(function (e){
            e.preventDefault();

            let msg = '';

            donateVisaNameLast = $("input[name='donate-visa-name-last']").val();
            donateVisaCompany = $("input[name='donate-visa-company']").val();
            donateVisaEmail = $("input[name='donate-visa-email']").val();

            if (!donatevisadvsmp.apikey)
                msg += donatevisadvsmp.apikeyMsj;

            if (msg != ''){
                $(".donate-visa-alert").append("<span style='color:red;font-size:18px'>"+msg+"<br></span>");
                return;
            }

            $('button[type="submit"]').prop('disabled', true);

            $('.v-checkout-wrapper').show();


            $('.v-button').click();
            
        });

    });
})(jQuery);