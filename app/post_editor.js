document.addEventListener('DOMContentLoaded', () => {

    // -------------------------
    // Elementi DOM
    // -------------------------
    const titleInput = document.getElementById('post-title');
    const bodyInput  = document.getElementById('post-body');
    const titleCount = document.getElementById('title-count');
    const bodyCount  = document.getElementById('body-count');
    const saveStatus = document.getElementById('save-status');
    const dropZone   = document.getElementById('drop-zone');
    const fileInput  = document.getElementById('post-images');
    const previews   = document.getElementById('image-previews');
    const btnDraft   = document.getElementById('btn-draft');
    const btnPublish = document.getElementById('btn-publish');

    // Sicurezza: se uno degli elementi non esiste nel DOM lo script si ferma
    // (accade quando la sezione publisher non viene renderizzata da PHP)
    if (!titleInput || !bodyInput || !dropZone) return;

    // -------------------------
    // Contatori caratteri
    // -------------------------
    titleInput.addEventListener('input', () => {
        titleCount.textContent = titleInput.value.length;
    });

    bodyInput.addEventListener('input', () => {
        bodyCount.textContent = bodyInput.value.length;
    });

    // -------------------------
    // Toolbar formattazione
    // -------------------------
    const formatMap = {
        bold:      sel => `**${sel}**`,
        italic:    sel => `*${sel}*`,
        underline: sel => `__${sel}__`,
        h2:        sel => `\n## ${sel}`,
        h3:        sel => `\n### ${sel}`,
        ul:        sel => `\n- ${sel}`,
        ol:        sel => `\n1. ${sel}`,
        quote:     sel => `\n> ${sel}`,
        link:      sel => `[${sel || 'testo'}](url)`,
    };

    function applyFormat(type) {
        const start     = bodyInput.selectionStart;
        const end       = bodyInput.selectionEnd;
        const sel       = bodyInput.value.substring(start, end);
        const formatted = formatMap[type](sel);

        bodyInput.value =
            bodyInput.value.substring(0, start) +
            formatted +
            bodyInput.value.substring(end);

        bodyCount.textContent = bodyInput.value.length;
        bodyInput.focus();

        const newPos = start + formatted.length;
        bodyInput.setSelectionRange(newPos, newPos);
    }

    document.querySelectorAll('[data-fmt]').forEach(btn => {
        btn.addEventListener('click', () => applyFormat(btn.dataset.fmt));
    });

    // -------------------------
    // Upload e drag & drop
    // -------------------------
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('bg-light');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('bg-light');
    });

    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('bg-light');
        addPreviews(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', () => {
        addPreviews(fileInput.files);
        // Reset per permettere di ricaricare lo stesso file
        fileInput.value = '';
    });

    function addPreviews(files) {
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) {
                setStatus(`"${file.name}" non è un'immagine valida.`);
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                setStatus(`"${file.name}" supera i 5 MB e non è stata aggiunta.`);
                return;
            }

            const reader = new FileReader();
            reader.onload = ev => {
                const wrap = document.createElement('div');
                wrap.style.cssText = 'position:relative;width:72px;height:72px';

                const img = document.createElement('img');
                img.src = ev.target.result;
                img.alt = file.name;
                img.style.cssText =
                    'width:100%;height:100%;object-fit:cover;' +
                    'border-radius:8px;border:1px solid #dee2e6';

                const removeBtn = document.createElement('button');
                removeBtn.textContent = '✕';
                removeBtn.title = 'Rimuovi';
                removeBtn.type = 'button';
                removeBtn.style.cssText =
                    'position:absolute;top:2px;right:2px;width:18px;height:18px;' +
                    'border-radius:50%;background:rgba(0,0,0,.55);border:none;' +
                    'color:#fff;font-size:11px;cursor:pointer;line-height:1';
                removeBtn.addEventListener('click', e => {
                    e.stopPropagation();
                    wrap.remove();
                });

                wrap.appendChild(img);
                wrap.appendChild(removeBtn);
                previews.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });
    }

    // -------------------------
    // Validazione
    // -------------------------
    function validate() {
        if (!titleInput.value.trim()) {
            setStatus('Il titolo è obbligatorio.');
            titleInput.focus();
            return false;
        }
        if (!bodyInput.value.trim()) {
            setStatus("Il corpo dell'articolo è obbligatorio.");
            bodyInput.focus();
            return false;
        }
        return true;
    }

    function setStatus(message) {
        saveStatus.textContent = message;
    }

    function formatTime() {
        return new Date().toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
    }

    function collectFormData() {
        const formData = new FormData();
        formData.append('title', titleInput.value.trim());
        formData.append('body',  bodyInput.value.trim());
        Array.from(fileInput.files).forEach(file => formData.append('images[]', file));
        return formData;
    }

    // -------------------------
    // Salva bozza
    // -------------------------
    btnDraft.addEventListener('click', () => {
        if (!titleInput.value.trim()) {
            setStatus('Inserisci un titolo prima di salvare.');
            titleInput.focus();
            return;
        }

        // TODO: fetch('/api/posts/draft', { method: 'POST', body: collectFormData() })
        //     .then(res => res.json())
        //     .then(() => setStatus(`Bozza salvata — ${formatTime()}`))
        //     .catch(() => setStatus('Errore durante il salvataggio.'));

        setStatus(`Bozza salvata — ${formatTime()}`);
    });

    // -------------------------
    // Pubblica
    // -------------------------
    btnPublish.addEventListener('click', () => {
        if (!validate()) return;

        setStatus('Pubblicazione in corso…');

        // TODO: fetch('/api/posts/publish', { method: 'POST', body: collectFormData() })
        //     .then(res => res.json())
        //     .then(() => setStatus('Articolo pubblicato.'))
        //     .catch(() => setStatus('Errore durante la pubblicazione.'));
    });

});