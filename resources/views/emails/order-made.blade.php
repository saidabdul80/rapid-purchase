<p>Dear {{ $order->product->user->name }},</p>

<p>A new order has been made for your product:</p>

<p>Product Name: {{ $order->product->name }}</p>
<p>Quantity: {{ $order->quantity }}</p>
<p>Unit Price: {{ $order->unit_price }}</p>

<p>Thank you for your attention.</p>

<p>Best regards,<br>RapidPurchase Team</p>
