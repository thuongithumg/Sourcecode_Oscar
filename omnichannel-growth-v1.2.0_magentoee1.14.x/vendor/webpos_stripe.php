<?php

// Stripe singleton
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Stripe.php');

// Utilities
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Util/AutoPagingIterator.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Util/LoggerInterface.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Util/DefaultLogger.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Util/RequestOptions.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Util/Set.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Util/Util.php');

// HttpClient
require(dirname(dirname(__FILE__)) . '/lib/Stripe/HttpClient/ClientInterface.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/HttpClient/CurlClient.php');

// Errors
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/Base.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/Api.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/ApiConnection.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/Authentication.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/Card.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/InvalidRequest.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/Permission.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/RateLimit.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/SignatureVerification.php');

// OAuth errors
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/OAuth/OAuthBase.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/OAuth/InvalidGrant.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/OAuth/InvalidRequest.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/OAuth/InvalidScope.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/OAuth/UnsupportedGrantType.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Error/OAuth/UnsupportedResponseType.php');

// Plumbing
require(dirname(dirname(__FILE__)) . '/lib/Stripe/ApiResponse.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/JsonSerializable.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/StripeObject.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/ApiRequestor.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/ApiResource.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/SingletonApiResource.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/AttachedObject.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/ExternalAccount.php');

// Stripe API Resources
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Account.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/AlipayAccount.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/ApplePayDomain.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/ApplicationFee.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/ApplicationFeeRefund.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Balance.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/BalanceTransaction.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/BankAccount.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/BitcoinReceiver.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/BitcoinTransaction.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Card.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Charge.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Collection.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/CountrySpec.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Coupon.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Customer.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Dispute.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/EphemeralKey.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Event.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/FileUpload.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Invoice.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/InvoiceItem.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/LoginLink.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Order.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/OrderReturn.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Payout.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Plan.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Product.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Recipient.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/RecipientTransfer.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Refund.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/SKU.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Source.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Subscription.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/SubscriptionItem.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/ThreeDSecure.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Token.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Transfer.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/TransferReversal.php');

// OAuth
require(dirname(dirname(__FILE__)) . '/lib/Stripe/OAuth.php');

// Webhooks
require(dirname(dirname(__FILE__)) . '/lib/Stripe/Webhook.php');
require(dirname(dirname(__FILE__)) . '/lib/Stripe/WebhookSignature.php');
