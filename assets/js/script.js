document.addEventListener("DOMContentLoaded", () => {
    // Animation des messages flash (success ou erreur)
    const messages = document.querySelectorAll('.message-success, .message-erreur');
    messages.forEach(msg => {
        msg.style.opacity = 1;
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = 0;
        }, 4000);
    });

    // Formulaire newsletter avec AJAX
    const newsletterForm = document.querySelector('#newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const emailInput = newsletterForm.querySelector('input[name="email"]');
            const email = emailInput.value.trim();

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert("Adresse e-mail invalide.");
                return;
            }

            const formData = new FormData();
            formData.append('email', email);

            const response = await fetch('../ajax/newsletter_ajax.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            const feedback = document.createElement('p');
            feedback.className = result.success ? 'message-success' : 'message-erreur';
            feedback.textContent = result.message;
            newsletterForm.appendChild(feedback);

            setTimeout(() => feedback.remove(), 5000);
            if (result.success) emailInput.value = '';
        });
    }

    //  Confirmation automatique des suppressions (fallback)
    const formsSupprimer = document.querySelectorAll('form[action*="supprimer"]');
    formsSupprimer.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm("Confirmer la suppression ?")) {
                e.preventDefault();
            }
        });
    });

    // Bouton retour en arrière pour page en construction
    const backBtn = document.querySelector('#back-button');
    if (backBtn) {
        backBtn.addEventListener('click', (e) => {
            e.preventDefault();
            history.back();
        });
    }

    //  Animation au clic sur boutons (ex : panier, commande)
    const boutons = document.querySelectorAll('button, a.btn');
    boutons.forEach(b => {
        b.addEventListener('mousedown', () => b.classList.add('active'));
        b.addEventListener('mouseup', () => b.classList.remove('active'));
        b.addEventListener('mouseleave', () => b.classList.remove('active'));
    });

    //  Animation loader 
    const loader = document.querySelector('.loader');
    if (loader) {
        setTimeout(() => {
            loader.classList.add('fade-out');
            setTimeout(() => loader.remove(), 800);
        }, 1000);
    }

    //  Scroll doux pour ancres internes
    const internalLinks = document.querySelectorAll('a[href^="#"]');
    internalLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    //  Confirmation panier non connecté (depuis produit.php)
    const modalBtn = document.querySelector('.modal-ajout button');
    if (modalBtn) {
        modalBtn.addEventListener('click', () => {
            if (confirm("Vous devez être connecté pour ajouter au panier.\nSe connecter maintenant ?")) {
                window.location.href = 'connexion.php';
            }
        });
    }
//  Menu burger responsive
const burgerBtn = document.getElementById('burger-btn');
const nav = document.getElementById('main-nav');

if (burgerBtn && nav) {
    burgerBtn.addEventListener('click', () => {
        nav.classList.toggle('nav-open');
        nav.classList.toggle('nav-closed');
        burgerBtn.classList.toggle('open');
    });



}

});
const loader = document.querySelector('.loader');
if (loader) {
    setTimeout(() => {
        loader.classList.add('fade-out');
        setTimeout(() => loader.remove(), 800);
    }, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    const toggleSubmenu = document.querySelector('.toggle-submenu');
    const submenu = document.querySelector('.submenu');

    if (toggleSubmenu && submenu) {
        toggleSubmenu.addEventListener('click', (e) => {
            e.preventDefault();
            submenu.classList.toggle('open');
        });
    }
});
document.querySelectorAll('.btn-supprimer').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();

        if (!confirm('Supprimer cet article ?')) return;

        const form = this.closest('form');
        const formData = new FormData(form);

        fetch('../ajax/panier_supprimer.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload(); // ou supprimer le <tr> dynamiquement
            } else {
                alert(data.message || "Erreur");
            }
        })
        .catch(() => alert("Une erreur est survenue."));
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const formAjout = document.querySelector('#ajout-panier-form');

    if (formAjout) {
        formAjout.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(formAjout);

            try {
                const response = await fetch('../ajax/panier_ajout.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert("Produit ajouté au panier !");
                } else {
                    alert(result.message || "Une erreur est survenue.");
                }
            } catch (error) {
                alert("Erreur lors de l'ajout au panier.");
                console.error(error);
            }
        });
    }
});
document.addEventListener('DOMContentLoaded', () => {
    const formCommande = document.querySelector('#form-commande');

    if (formCommande) {
        formCommande.addEventListener('submit', async (e) => {
            e.preventDefault();

            try {
                const response = await fetch('../ajax/commande_valider.php', {
                    method: 'POST',
                    body: new FormData(formCommande)
                });

                const result = await response.json();

                if (result.success) {
                    alert("Commande validée !");
                    window.location.href = "mes_commandes.php?confirm=" + result.commande_id;
                } else {
                    alert(result.message || "Erreur lors de la validation.");
                }
            } catch (error) {
                alert("Une erreur s'est produite lors de la commande.");
                console.error(error);
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const tableBody = document.querySelector('.table-admin tbody');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim();

            // Si la recherche est vide, recharger tous les produits
            if (query === '') {
                fetch('../ajax/recherche_produits.php?q=*')
                    .then(res => res.json())
                    .then(allProduits => {
                        tableBody.innerHTML = '';
                        afficherProduits(allProduits);
                    });
                return;
            }

            // Sinon, effectuer une recherche filtrée
            fetch('../ajax/recherche_produits.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    tableBody.innerHTML = '';

                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="7">Aucun produit trouvé.</td></tr>';
                        return;
                    }

                    afficherProduits(data);
                });
        });
    }

    // Fonction d'affichage des produits dans le tableau
    function afficherProduits(data) {
        data.forEach(produit => {
            const row = `
                <tr>
                    <td>${produit.id}</td>
                    <td>${produit.nom}</td>
                    <td>${produit.categorie_nom || 'Non défini'}</td>
                    <td>${parseFloat(produit.prix).toFixed(2).replace('.', ',')} €</td>
                    <td>${produit.stock}</td>
                    <td>${produit.actif == 1 ? 'Actif' : 'Inactif'}</td>
                    <td>
                        <a href="produits_modifier.php?id=${produit.id}">Modifier</a> |
                        <a href="produits_supprimer.php?id=${produit.id}" onclick="return confirm('Confirmer la suppression de ce produit ?');">Supprimer</a>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    }
});



