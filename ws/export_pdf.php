<?php
require('fpdf/fpdf.php');

class PDF extends FPDF {
    private $pretData;
    private $amortissementData;

    function __construct($pretData, $amortissementData) {
        parent::__construct();
        $this->pretData = $pretData;
        $this->amortissementData = $amortissementData;
    }

    function Header() {
        // Logo ou en-tête
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Etablissement Financier', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'ID Pret:' . $this->pretData['id'], 0, 1, 'C');
        $this->Ln(5);
        
        // Date d'édition
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Edite le : ' . date('d/m/Y H:i'), 0, 1, 'R');
        $this->Ln(5);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    function ChapterTitle($label, $fillColor = array(240, 240, 240)) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
        $this->Cell(0, 8, $label, 0, 1, 'L', true);
        $this->Ln(2);
    }
    
    function InfoPret() {
        $this->ChapterTitle('Informations du pret');
        
        // Colonne gauche
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 7, 'Nom du client:', 0, 0);
        $this->Cell(0, 7, $this->pretData['nom_client'], 0, 1);
        
        $this->Cell(60, 7, 'Prenom du client:', 0, 0);
        $this->Cell(0, 7, $this->pretData['prenom_client'], 0, 1);
        
        $this->Cell(60, 7, 'Type de pret:', 0, 0);
        $this->Cell(0, 7, $this->pretData['type_pret'], 0, 1);
        
        $this->Cell(60, 7, 'Capital de depart:', 0, 0);
        $this->Cell(0, 7, number_format($this->pretData['montant'], 2, ',', ' ') . ' Ar', 0, 1);
        
        // Colonne droite
        $this->Cell(60, 7, 'Taux d\'interet:', 0, 0);
        $this->Cell(0, 7, $this->pretData['taux'] . '%', 0, 1);
        
        $this->Cell(60, 7, 'Assurance:', 0, 0);
        $this->Cell(0, 7, $this->pretData['assurance'] . '%', 0, 1);
        
        $this->Cell(60, 7, 'Duree:', 0, 0);
        $this->Cell(0, 7, $this->pretData['duree'] . ' mois', 0, 1);
        
        $this->Cell(60, 7, 'Date de debut:', 0, 0);
        $this->Cell(0, 7, date('d/m/Y', strtotime($this->pretData['date_debut'])), 0, 1);
        
        $this->Cell(60, 7, 'Statut:', 0, 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 7, ucfirst($this->pretData['statut']), 0, 1);
        
        $this->Ln(10);
    }
    
    function TableAmortissement() {
        $this->ChapterTitle('Tableau d\'amortissement', array(230, 230, 255));
        
        // En-têtes
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(15, 8, 'Mois', 1, 0, 'C');
        $this->Cell(25, 8, 'Date', 1, 0, 'C');
        $this->Cell(30, 8, 'Capital', 1, 0, 'C');
        $this->Cell(30, 8, 'Interets', 1, 0, 'C');
        $this->Cell(30, 8, 'Mensualite', 1, 0, 'C');
        $this->Cell(40, 8, 'Capital restant', 1, 1, 'C');
        
        // Données
        $this->SetFont('Arial', '', 8);
        foreach ($this->amortissementData as $echeance) {
            $this->Cell(15, 6, $echeance['mois'], 1, 0, 'C');
            $this->Cell(25, 6, date('d/m/Y', strtotime($echeance['date_echeance'])), 1, 0, 'C');
            $this->Cell(30, 6, number_format($echeance['capital'], 2, ',', ' ') . ' Ar', 1, 0, 'R');
            $this->Cell(30, 6, number_format($echeance['interets'], 2, ',', ' ') . ' Ar', 1, 0, 'R');
            $this->Cell(30, 6, number_format($echeance['mensualite'], 2, ',', ' ') . ' Ar', 1, 0, 'R');
            $this->Cell(40, 6, number_format($echeance['capital_restant'], 2, ',', ' ') . ' Ar', 1, 1, 'R');
        }
        
        $this->Ln(10);
    }
    
    function ResumePret() {
        $this->ChapterTitle('Resume du pret', array(230, 255, 230));
        
        // Calcul des totaux
        $totalCapital = 0;
        $totalInterets = 0;
        
        foreach ($this->amortissementData as $echeance) {
            $totalCapital += $echeance['capital'];
            $totalInterets += $echeance['interets'];
        }
        
        $capitalRestant = $this->pretData['montant'] - $totalCapital;
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(90, 7, 'Montant total rembourse:', 0, 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 7, number_format($totalCapital + $totalInterets, 2, ',', ' ') . ' Ar', 0, 1);
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(90, 7, 'Interets totaux:', 0, 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 7, number_format($totalInterets, 2, ',', ' ') . ' Ar', 0, 1);
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(90, 7, 'Reste a payer:', 0, 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 7, number_format(max(0, $capitalRestant), 2, ',', ' ') . ' Ar', 0, 1);
    }
}

// Récupérer l'ID du prêt
$pretId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($pretId <= 0) {
    die('ID de pret invalide');
}

// URL de l'API - à adapter selon votre configuration
$basePath = 'C:/xampp/htdocs/Exo/S4/ETU_3346_ETU_3351_ETU_3358/ws';
$apiBase = 'http://localhost/Exo/S4/ETU_3346_ETU_3351_ETU_3358/ws';



// Pour inclure FPDF (utilisation du chemin absolu)
function fetchData($url) {
    // Solution hybride ultra-robuste
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        $response = curl_exec($ch);
        curl_close($ch);
    } else {
        $response = @file_get_contents($url);
    }

    if (!$response) {
        die("Erreur: Impossible d'accéder à l'API. URL essayée: " . $url);
    }

    $data = json_decode($response, true);
    return $data ?: die("Réponse API invalide: " . substr($response, 0, 200));
}

// Récupérer les données du prêt
$pretData = fetchData($apiBase . '/prets/' . $pretId);

// Récupérer le tableau d'amortissement
$amortissementData = fetchData($apiBase . '/prets/' . $pretId . '/amortissement');

// Création du PDF
$pdf = new PDF($pretData, $amortissementData);
$pdf->AliasNbPages();
$pdf->AddPage();

// Ajout des sections
$pdf->InfoPret();
$pdf->TableAmortissement();
$pdf->ResumePret();

// Génération du PDF
$pdf->Output('D', 'pret_' . $pretId . '_' . date('Y-m-d') . '.pdf');
?>