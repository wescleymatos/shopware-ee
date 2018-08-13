{extends file='parent:frontend/checkout/change_payment.tpl'}

{block name='frontend_checkout_payment_fieldset_template'}
    {assign "payment" $payment_mean.name|substr:0:23}
    {if $payment == "wirecard_elastic_engine"}
        {if $payment_mean.name == "wirecard_elastic_engine_credit_card"}
            <div class="wirecard-elastic-engine--payments" >
                <img title="{s name="CreditCardTitle" namespace="frontend/wirecard_elastic_engine/payments"}Credit Card{/s}"
                     src="{s name="CreditCardLogo" namespace="frontend/wirecard_elastic_engine/payments"}{/s}" />
            </div>
        {elseif $payment_mean.name == "wirecard_elastic_engine_paypal"}
            <div class="wirecard-elastic-engine--payments" >
                <img title="{s name="PayPalTitle" namespace="frontend/wirecard_elastic_engine/payments"}PayPal{/s}"
                     src="{s name="PayPalLogo" namespace="frontend/wirecard_elastic_engine/payments"}{/s}" />
            </div>
        {elseif $payment_mean.name == "wirecard_elastic_engine_sepa"}
            <div class="wirecard-elastic-engine--payments" >
                <img title="{s name="SepaTitle" namespace="frontend/wirecard_elastic_engine/payments"}SEPA{/s}"
                     src="{s name="SepaLogo" namespace="frontend/wirecard_elastic_engine/payments"}{/s}" />
            </div>
        {elseif $payment_mean.name == "wirecard_elastic_engine_sofort"}
            <div class="wirecard-elastic-engine--payments" >
                <img title="{s name="SofortTitle" namespace="frontend/wirecard_elastic_engine/payments"}Sofort.{/s}"
                     src="{s name="SofortLogo" namespace="frontend/wirecard_elastic_engine/payments"}{/s}" />
            </div>
        {/if}
    {/if}
    {$smarty.block.parent}
{/block}