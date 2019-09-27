/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

define(['jquery'], function ($) 
{
    'use strict';
    
    return function (widget) 
    {
    	$.widget('mage.SwatchRenderer', widget, 
    	{
    		_UpdatePrice: function()
    		{
    			this._super();
    			
    			var context = this;
    			
    			(function(callback)
    			{
    				if (AEC.Const.COOKIE_DIRECTIVE)
    				{
    					AEC.CookieConsent.queue(callback).process();
    				}
    				else 
    				{
    					callback.apply(window,[]);
    				}
    			})
    			(
    				(function(context)
    				{
    					if (context && 'undefined' !== typeof context.getProduct() && 'undefined' !== typeof AEC.CONFIGURABLE_SIMPLES)
    					{
	    					var simple = {}, key = context.getProduct().toString();
	    					
	    					if (AEC.CONFIGURABLE_SIMPLES.hasOwnProperty(key))
	    					{
	    						simple = AEC.CONFIGURABLE_SIMPLES[key];
	    					}
	    					
	    					return function()
	    					{
	    						dataLayer.push(
	    						{
	    							'event':'virtualVariantView',
	    							'ecommerce':
	    							{
	    								'currencyCode': AEC.currencyCode,
	    								'detail':
	    								{
	    									'actionField':
											{
												'list':'Configurable variants'
											},
											'products':[simple]
	    								}
	    							}
	    						});
	    						
	    						if ("undefined" !== typeof fbq)
	    		        		{
	    							fbq("track", "CustomizeProduct");
	    		        		}
	    					}
	    				}
    					else 
    					{
    						dataLayer.push({ 'event':'resetsSwatchSelection' });
    					}
    					
    				})(this)
    			);
    		}
        });
    	
    	return $.mage.SwatchRenderer;
    }
});