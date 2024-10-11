function validateForm(formData) {
    const requiredFields = [
      "titre",
      "description",
      "prix",
      "stock",
      "marque",
      "collection",
    ];
    const errors = [];
  
    for (const field of requiredFields) {
      if (!formData.get(field)) {
        errors.push(`Le champ "${field}" est obligatoire.`);
      }
    }
  
    if (formData.get("prix") && isNaN(parseFloat(formData.get("prix")))) {
      errors.push("Le prix doit être un nombre valide.");
    }
  
    if (formData.get("stock") && isNaN(parseInt(formData.get("stock")))) {
      errors.push("Le stock doit être un nombre entier valide.");
    }
  
    return errors;
  }