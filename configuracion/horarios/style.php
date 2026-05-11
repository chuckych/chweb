:root {
    --primary-purple: #4B0082;
    --bg-light: #f8f9fa;
    --border-color: #ddd;
}

.jscolor-btn-close {
    border: 1px solid var(--border-color);
    background-color: transparent !important;
}

.config-card {
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
    padding: 1.5rem;
}

.header-inputs .form-control {
    border-radius: 4px;
}

.day-label {
    font-weight: 700;
    font-size: 1rem;
    color: #444;
}

.form-label-sm {
    font-size: 0.75rem;
    /* text-transform: uppercase; */
    /* color: #6c757d; */
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.timeline-container {
    position: relative;
    height: 40px;
    background: #f1f3f5;
    border-radius: 4px;
    margin-top: 1.5rem;
    display: flex;
}

.timeline-hour {
    flex: 1;
    border-left: 1px solid #e0e0e0;
    position: relative;
    height: 100%;
}

.timeline-hour>span {
    position: absolute;
    top: -18px;
    left: -4px;
    font-size: 0.65rem;
    color: #888;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
}

.timeline-next-day {
    margin-left: 4px;
    font-weight: 600;
    color: #666;
    position: static;
    top: auto;
    left: auto;
    display: inline;
}

.timeline-bar {
    position: absolute;
    height: 12px;
    background-color: var(--main-bg-modcolor);
    top: 50%;
    transform: translateY(-50%);
    border-radius: 2px;
}

td {
    vertical-align: middle !important;
}

th {
    vertical-align: middle !important;
    font-size: 1.2em;
    color: #333;
}

.LaboralID {
    /* background-color: #d4edda; */
    /* border-bottom: 3px solid #d4edda; */
    border-radius: 4px;
    font-size: 1em;
    padding: 2px 6px;
    /* opacity: 0.7; */
}

tr.dtrg-group>td {
    /* background-color: #f8f9fa !important; */
    border: 0px solid var(--border-color);
    font-size: 0.9em;
    color: #495057 !important;
    margin: 0px 0 !important;
    padding: 0px !important;
    border-top: 1px solid var(--border-color);
}

.table tr.dtrg-group {
    box-shadow: none !important;
}

.table td {
    border-top: 0px solid var(--border-color);
}

#tblUnused.table td {
    border-top: 1px solid var(--border-color);
}
.custom-file-input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    background: #fff;
    font-size: 14px;
    color: #495057;
    cursor: pointer;
}

/* Botón interno */
.custom-file-input::file-selector-button {
    margin-right: 12px;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    background: #0d6efd;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s ease;
}

/* Hover */
.custom-file-input::file-selector-button:hover {
    background: #0b5ed7;
}

/* Focus */
.custom-file-input:focus {
    outline: none;
    border-color: #86b7fe;
    box-shadow: 0 0 0 3px rgba(13,110,253,.25);
}
.btn-upload {
    display: inline-block;
    padding: 10px 16px;
    background: #198754;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.btn-upload:hover {
    background: #157347;
}