<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            table-layout:fixed;
        }

        .invoice-box table td {
            padding: 10px;
            vertical-align: top;
        }

        .invoice-box table tr.top td:nth-child(2),
        .invoice-box table tr.information td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            text-align: center;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
            text-align: center;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            text-align: right;
            border-right: 1px solid #eee;
        }

        .invoice-box table tr.item td:last-of-type {
            border-right: 0px;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
            border-bottom: 2px solid #eee;
        }

        .invoice-box table tr.total td:nth-child(1) {
            text-align: center;
            border-right: 2px solid #eee;
        }

        .invoice-box table tr.total td:nth-child(2) {
            text-align: right;
        }

        .last-table td {
            text-align: center;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <img class="logo" src="assets/admin/images/logo.jpeg" style="max-width:300px; width: 50%;">
                </td>
                <td colspan="2">
                    Invoice #: 123<br>
                    Date: January 1, 2020<br>
                    Customer ID : 20
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <b>Troubleshooters Pvt Ltd.</b><br>
                    East Kailash,<br>
                    New Delhi, India<br><br>
                    PAN - ABCD1234D<br>
                    GST - 122222222222
                </td>
                <td colspan="2">
                    Vishal Kr. Verma<br>
                    East Delhi<br>
                    vishal@gmail.com
                </td>
            </tr>

            <tr>
                <td colspan="4"><b>Added Products / Materials</b></td>
            </tr>

            <tr class="heading">
                <td>
                    Description
                </td>

                <td>
                    Qty
                </td>

                <td>
                    Unit Price
                </td>

                <td>
                    Amount
                </td>
            </tr>

            <tr class="details">
                <td>
                    Cement
                </td>

                <td>
                    2
                </td>

                <td>
                    50
                </td>

                <td>
                    5000
                </td>
            </tr>

            <tr class="heading">
                <td>
                    Description
                </td>

                <td>
                    Qty
                </td>

                <td>
                    Unit Price
                </td>

                <td>
                    Amount
                </td>
            </tr>

            <tr class="item">
                <td>
                    Cement
                </td>

                <td>
                    2
                </td>

                <td>
                    50
                </td>

                <td>
                    5000
                </td>
            </tr>

            <tr class="item">
                <td>
                    Cement
                </td>
                <td>
                    2
                </td>

                <td>
                    50
                </td>

                <td>
                    5000
                </td>
            </tr>

            <tr class="item last">
                <td>
                    Cement
                </td>

                <td>
                    2
                </td>

                <td>
                    50
                </td>

                <td>
                    5000
                </td>
            </tr>

            <tr class="total">
                <td colspan="2">
                    <i>Thanks for your business</i>
                </td>

                <td colspan="2">
                    TOTAL : &#8377; 385.00
                </td>
            </tr>
        </table>

        <table class="last-table">
            <tr>
                <td>If you have any sessions about this invoice, please contact</td>
            </tr>

            <tr>
                <td>abc@gmail.com</td>
            </tr>
        </table>
    </div>
</body>

</html>