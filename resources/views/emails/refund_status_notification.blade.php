<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #a7a7a7fd;
            color: #FFF;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .content {
            margin: 20px 0;
            line-height: 35px;
        }
        .rh-comment {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Bonjour <span>{{ $user->firstname }} {{$user->lastname}},</span></h1>
    </div>
    <div class="content">
        <p>{{ $refund->status === 'accepted' ? 'Nous sommes ravis de vous informer que ' : 'Malheureusement, ' }}votre demande a été  <strong> {{ $refund->status === 'accepted' ? 'approuvée' : 'rejetée' }}</strong>. Vous trouverez ci-dessous les détails de votre demande :</p>
        <ul>
            <li><strong>Date de la demande :</strong> {{ $refund->created_at }}</li>
            <li><strong>Type de frais :</strong> {{ $typeFee->title }}</li>
            <li><strong>Montant {{ $refund->status === 'accepted' ? 'approuvé' : 'rejeté' }} :</strong> {{ $refund->reimbursement_amount }} TND</li>
        </ul>
        <p class="rh-comment">{{ $refund->HR_comment }}</p>
        <p>Merci pour votre confiance en nos services. Si vous avez des questions, n'hésitez pas à nous contacter.</p>
    </div>
    <div class="footer">
        <p>&copy; 2024 TAC-TIC. Tous droits réservés.</p>
        <p><a href="https://www.tac-tic.com" style="color: #0073e6; text-decoration: none;">Visitez notre site web</a></p>
    </div>
</div>
</body>
</html>

