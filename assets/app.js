
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

// Import du fichier CSS natif de Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';

// Import des composants JavaScript de Bootstrap
import 'bootstrap';

// Import de vos styles personnalisés si nécessaires
import './styles/app.css';

// Exemple de message dans la console pour vérifier que le fichier est bien chargé
console.log('Bienvenue dans votre fichier assets/app.js - Bootstrap est chargé avec succès ! 🎉');
