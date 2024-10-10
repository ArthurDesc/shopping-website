function loadArticlesList() {
    // Appel à l'API pour récupérer la liste des articles
    API.getArticles().then((articles) => {
      const articlesList = document.getElementById("articles-list");
      articlesList.innerHTML = articles
        .map(
          (article) => `
              <div class="article-item">
                  <h3>${article.nom}</h3>
                  <p>${article.description}</p>
                  <p>Prix : ${article.prix} €</p>
              </div>
          `
        )
        .join("");
    });
  }