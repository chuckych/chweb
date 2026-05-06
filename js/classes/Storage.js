/**
 * Clase Storage - Interfaz unificada para almacenamiento local y de sesión
 * 
 * Proporciona métodos para trabajar con localStorage, sessionStorage e IndexedDB
 * con características adicionales como expiración, valores por defecto y
 * serialización automática de objetos complejos.
 * @author Norberto CH
 * @version 1.0
 */
class Storage {
    /**
     * Constructor
     * @param {string} type - Tipo de almacenamiento ('local', 'session' o 'indexed')
     * @param {Object} options - Opciones adicionales
     */
    constructor(type = 'local', options = {}) {
        this.type = type.toLowerCase();
        this.prefix = options.prefix || '';
        this.defaultExpiry = options.defaultExpiry || null; // Tiempo en milisegundos
        this.encryption = options.encryption || false;
        this.encryptionKey = options.encryptionKey || 'appKey123';
        
        // Para IndexedDB
        this.dbName = options.dbName || 'appStorage';
        this.storeName = options.storeName || 'keyValueStore';
        this.dbVersion = options.dbVersion || 1;
        this.db = null;
        
        if (this.type === 'indexed') {
            this.storageType = 'indexedDB';
            // Inicializar IndexedDB
            this._initIndexedDB();
        } else {
            this.storage = this.type === 'session' ? sessionStorage : localStorage;
            this.storageType = this.type === 'session' ? 'sessionStorage' : 'localStorage';
        }
    }
    
    /**
     * Inicializa la conexión con IndexedDB
     * @returns {Promise} - Promesa que se resuelve cuando la conexión está lista
     * @private
     */
    _initIndexedDB() {
        // Si IndexedDB no está disponible, usar localStorage como fallback
        if (!window.indexedDB) {
            console.warn('IndexedDB no está disponible. Usando localStorage como alternativa.');
            this.storage = localStorage;
            this.storageType = 'localStorage';
            this.type = 'local';
            return Promise.resolve();
        }
        
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);
            
            request.onerror = (event) => {
                console.error('Error al abrir IndexedDB:', event.target.error);
                // Usar localStorage como fallback
                this.storage = localStorage;
                this.storageType = 'localStorage';
                this.type = 'local';
                reject(event.target.error);
            };
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Crear el object store si no existe
                if (!db.objectStoreNames.contains(this.storeName)) {
                    db.createObjectStore(this.storeName, { keyPath: 'key' });
                    // console.log(`Object store "${this.storeName}" creado.`);
                }
            };
            
            request.onsuccess = (event) => {
                this.db = event.target.result;
                // console.log(`Conexión a IndexedDB "${this.dbName}" exitosa.`);
                resolve();
            };
        });
    }
    
    /**
     * Espera a que IndexedDB esté listo para usar
     * @returns {Promise} - Promesa que se resuelve cuando IndexedDB está listo
     * @private
     */
    _waitForDB() {
        if (this.type !== 'indexed') {
            return Promise.resolve();
        }
        
        if (this.db) {
            return Promise.resolve();
        }
        
        return new Promise((resolve) => {
            const check = () => {
                if (this.db) {
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            
            check();
        });
    }
    
    /**
     * Genera una clave con prefijo
     * @param {string} key - Clave original
     * @returns {string} - Clave con prefijo
     * @private
     */
    _getPrefixedKey(key) {
        return this.prefix ? `${this.prefix}_${key}` : key;
    }
    
    /**
     * Serializa un valor para almacenamiento
     * @param {any} value - Valor a serializar
     * @returns {string} - Valor serializado
     * @private
     */
    _serialize(value) {
        // Manejar caso especial de undefined
        if (value === undefined) {
            return JSON.stringify({ type: 'undefined' });
        }
        
        try {
            // Si hay expiración, crear un objeto que la contenga
            if (this._hasExpiry(value)) {
                const { value: actualValue, expiry } = value;
                return JSON.stringify({
                    value: actualValue,
                    expiry: expiry,
                    timestamp: new Date().getTime()
                });
            }
            
            // Serialización normal
            return JSON.stringify(value);
        } catch (error) {
            console.error('Error serializando valor:', error);
            return JSON.stringify(null);
        }
    }
    
    /**
     * Deserializa un valor desde el almacenamiento
     * @param {string} value - Valor serializado
     * @returns {any} - Valor deserializado
     * @private
     */
    _deserialize(value) {
        if (!value) return null;
        
        try {
            const parsed = JSON.parse(value);
            
            // Manejar caso especial de undefined
            if (parsed && parsed.type === 'undefined') {
                return undefined;
            }
            
            // Verificar si hay expiración
            if (this._hasExpiry(parsed)) {
                if (this._isExpired(parsed)) {
                    return null;
                }
                return parsed.value;
            }
            
            return parsed;
        } catch (error) {
            console.warn('Error deserializando valor:', error);
            return value; // Devolver el valor original si hay error
        }
    }
    
    /**
     * Verifica si un objeto tiene propiedades de expiración
     * @param {Object} obj - Objeto a verificar
     * @returns {boolean} - true si tiene propiedades de expiración
     * @private
     */
    _hasExpiry(obj) {
        return obj && typeof obj === 'object' && 
               (obj.expiry !== undefined || obj.timestamp !== undefined);
    }
    
    /**
     * Verifica si un valor ha expirado
     * @param {Object} obj - Objeto con timestamp y expiry
     * @returns {boolean} - true si ha expirado
     * @private
     */
    _isExpired(obj) {
        if (!obj || !obj.timestamp || !obj.expiry) {
            return false;
        }
        
        const now = new Date().getTime();
        return now - obj.timestamp > obj.expiry;
    }
    
    /**
     * Guarda un valor en el almacenamiento
     * @param {string} key - Clave para el valor
     * @param {any} value - Valor a almacenar
     * @param {number|null} expiry - Tiempo de expiración en milisegundos
     * @returns {boolean|Promise<boolean>} - Valor o Promesa según el tipo de almacenamiento
     */
    set(key, value, expiry = null) {
        const prefixedKey = this._getPrefixedKey(key);
        
        try {
            // Si se especifica expiración o hay expiración por defecto
            const expiryTime = expiry !== null ? expiry : this.defaultExpiry;
            
            // Si hay tiempo de expiración, encapsular el valor
            let finalValue = value;
            if (expiryTime !== null) {
                finalValue = {
                    value: value,
                    expiry: expiryTime,
                    timestamp: new Date().getTime()
                };
            }
            
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._setIndexedDB(prefixedKey, finalValue);
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                const serialized = this._serialize(finalValue);
                this.storage.setItem(prefixedKey, serialized);
                return true;
            }
        } catch (error) {
            console.error(`Error al guardar ${prefixedKey}:`, error);
            return this.type === 'indexed' ? Promise.resolve(false) : false;
        }
    }
    
    /**
     * Implementación privada de set para IndexedDB
     * @param {string} prefixedKey - Clave con prefijo
     * @param {any} value - Valor a almacenar
     * @returns {Promise<boolean>} - Promesa que se resuelve con true si se guardó correctamente
     * @private
     */
    async _setIndexedDB(prefixedKey, value) {
        await this._waitForDB();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            
            const serialized = this._serialize(value);
            const request = store.put({ 
                key: prefixedKey, 
                value: serialized,
                timestamp: new Date().getTime()
            });
            
            request.onsuccess = () => resolve(true);
            request.onerror = (event) => {
                console.error(`Error al guardar ${prefixedKey} en IndexedDB:`, event.target.error);
                reject(false);
            };
        });
    }
    
    /**
     * Recupera un valor del almacenamiento
     * @param {string} key - Clave del valor
     * @param {any} defaultValue - Valor por defecto si no existe o ha expirado
     * @returns {any|Promise<any>} - Valor o Promesa según el tipo de almacenamiento
     */
    get(key, defaultValue = null) {
        const prefixedKey = this._getPrefixedKey(key);
        
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._getIndexedDB(prefixedKey, defaultValue);
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                const value = this.storage.getItem(prefixedKey);
                
                if (value === null) {
                    return defaultValue;
                }
                
                const deserialized = this._deserialize(value);
                return deserialized === null ? defaultValue : deserialized;
            }
        } catch (error) {
            console.warn(`Error al recuperar ${prefixedKey}:`, error);
            return this.type === 'indexed' ? Promise.resolve(defaultValue) : defaultValue;
        }
    }
    
    /**
     * Implementación privada de get para IndexedDB
     * @param {string} prefixedKey - Clave con prefijo
     * @param {any} defaultValue - Valor por defecto 
     * @returns {Promise<any>} - Promesa que se resuelve con el valor almacenado
     * @private
     */
    async _getIndexedDB(prefixedKey, defaultValue) {
        await this._waitForDB();
        
        return new Promise((resolve) => {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const request = store.get(prefixedKey);
            
            request.onsuccess = (event) => {
                const result = event.target.result;
                
                if (!result) {
                    resolve(defaultValue);
                    return;
                }
                
                const deserialized = this._deserialize(result.value);
                resolve(deserialized === null ? defaultValue : deserialized);
            };
            
            request.onerror = (event) => {
                console.warn(`Error al recuperar ${prefixedKey} de IndexedDB:`, event.target.error);
                resolve(defaultValue);
            };
        });
    }
    
    /**
     * Verifica si existe una clave en el almacenamiento y no ha expirado
     * @param {string} key - Clave a verificar
     * @returns {boolean|Promise<boolean>} - Valor o Promesa según el tipo de almacenamiento
     */
    has(key) {
        const prefixedKey = this._getPrefixedKey(key);
        
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._hasIndexedDB(prefixedKey);
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                const value = this.storage.getItem(prefixedKey);
                
                if (value === null) {
                    return false;
                }
                
                // Deserializar para verificar expiración
                const deserialized = this._deserialize(value);
                return deserialized !== null;
            }
        } catch (error) {
            console.warn(`Error al verificar ${prefixedKey}:`, error);
            return this.type === 'indexed' ? Promise.resolve(false) : false;
        }
    }
    
    /**
     * Implementación privada de has para IndexedDB
     * @param {string} prefixedKey - Clave con prefijo
     * @returns {Promise<boolean>} - Promesa que se resuelve con true si la clave existe
     * @private
     */
    async _hasIndexedDB(prefixedKey) {
        await this._waitForDB();
        
        return new Promise((resolve) => {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const request = store.get(prefixedKey);
            
            request.onsuccess = (event) => {
                const result = event.target.result;
                
                if (!result) {
                    resolve(false);
                    return;
                }
                
                // Deserializar para verificar expiración
                const deserialized = this._deserialize(result.value);
                resolve(deserialized !== null);
            };
            
            request.onerror = () => resolve(false);
        });
    }
    
    /**
     * Elimina un valor del almacenamiento
     * @param {string} key - Clave a eliminar
     * @returns {boolean|Promise<boolean>} - Valor o Promesa según el tipo de almacenamiento
     */
    remove(key) {
        const prefixedKey = this._getPrefixedKey(key);
        
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._removeIndexedDB(prefixedKey);
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                this.storage.removeItem(prefixedKey);
                return true;
            }
        } catch (error) {
            console.error(`Error al eliminar ${prefixedKey}:`, error);
            return this.type === 'indexed' ? Promise.resolve(false) : false;
        }
    }
    
    /**
     * Implementación privada de remove para IndexedDB
     * @param {string} prefixedKey - Clave con prefijo
     * @returns {Promise<boolean>} - Promesa que se resuelve con true si se eliminó correctamente
     * @private
     */
    async _removeIndexedDB(prefixedKey) {
        await this._waitForDB();
        
        return new Promise((resolve) => {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            const request = store.delete(prefixedKey);
            
            request.onsuccess = () => resolve(true);
            request.onerror = (event) => {
                console.error(`Error al eliminar ${prefixedKey} de IndexedDB:`, event.target.error);
                resolve(false);
            };
        });
    }
    
    /**
     * Elimina todas las claves del almacenamiento
     * Si se especifica un prefijo en el constructor, solo elimina las que tienen ese prefijo
     * @returns {boolean|Promise<boolean>} - Valor o Promesa según el tipo de almacenamiento
     */
    clear() {
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._clearIndexedDB();
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                if (!this.prefix) {
                    // Sin prefijo, limpiar todo
                    this.storage.clear();
                    return true;
                }
                
                // Con prefijo, eliminar solo las claves que lo tienen
                const keys = Object.keys(this.storage);
                const prefixedKeys = keys.filter(key => 
                    key.startsWith(this.prefix + '_')
                );
                
                prefixedKeys.forEach(key => this.storage.removeItem(key));
                return true;
            }
        } catch (error) {
            console.error('Error al limpiar almacenamiento:', error);
            return this.type === 'indexed' ? Promise.resolve(false) : false;
        }
    }
    
    /**
     * Implementación privada de clear para IndexedDB
     * @returns {Promise<boolean>} - Promesa que se resuelve con true si se limpiaron correctamente
     * @private
     */
    async _clearIndexedDB() {
        await this._waitForDB();
        
        return new Promise((resolve) => {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            
            if (!this.prefix) {
                // Sin prefijo, limpiar todo
                const request = store.clear();
                request.onsuccess = () => resolve(true);
                request.onerror = (event) => {
                    console.error('Error al limpiar IndexedDB:', event.target.error);
                    resolve(false);
                };
            } else {
                // Con prefijo, necesitamos obtener todas las claves y filtrar
                const request = store.openCursor();
                const keysToDelete = [];
                
                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        if (cursor.key.startsWith(this.prefix + '_')) {
                            keysToDelete.push(cursor.key);
                        }
                        cursor.continue();
                    } else {
                        // Ya tenemos todas las claves, ahora borrarlas
                        const deletePromises = keysToDelete.map(key => 
                            new Promise((resolveDelete) => {
                                const deleteRequest = store.delete(key);
                                deleteRequest.onsuccess = () => resolveDelete();
                                deleteRequest.onerror = () => resolveDelete();
                            })
                        );
                        
                        Promise.all(deletePromises)
                            .then(() => resolve(true))
                            .catch(() => resolve(false));
                    }
                };
                
                request.onerror = () => resolve(false);
            }
        });
    }
    
    /**
     * Elimina todas las claves expiradas
     * @returns {number|Promise<number>} - Valor o Promesa según el tipo de almacenamiento
     */
    clearExpired() {
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._clearExpiredIndexedDB();
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                let count = 0;
                const keys = Object.keys(this.storage);
                
                // Si hay prefijo, filtrar solo las claves con ese prefijo
                const relevantKeys = this.prefix 
                    ? keys.filter(key => key.startsWith(this.prefix + '_'))
                    : keys;
                
                relevantKeys.forEach(key => {
                    const value = this.storage.getItem(key);
                    try {
                        const parsed = JSON.parse(value);
                        if (this._hasExpiry(parsed) && this._isExpired(parsed)) {
                            this.storage.removeItem(key);
                            count++;
                        }
                    } catch (e) {
                        // Ignorar errores de parsing
                    }
                });
                
                return count;
            }
        } catch (error) {
            console.error('Error al limpiar claves expiradas:', error);
            return this.type === 'indexed' ? Promise.resolve(0) : 0;
        }
    }
    
    /**
     * Implementación privada de clearExpired para IndexedDB
     * @returns {Promise<number>} - Promesa que se resuelve con el número de claves eliminadas
     * @private
     */
    async _clearExpiredIndexedDB() {
        await this._waitForDB();
        
        return new Promise((resolve) => {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            const request = store.openCursor();
            const keysToDelete = [];
            
            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    // Si hay prefijo, filtrar
                    if (!this.prefix || cursor.value.key.startsWith(this.prefix + '_')) {
                        try {
                            const parsed = JSON.parse(cursor.value.value);
                            if (this._hasExpiry(parsed) && this._isExpired(parsed)) {
                                keysToDelete.push(cursor.value.key);
                            }
                        } catch (e) {
                            // Ignorar errores de parsing
                        }
                    }
                    cursor.continue();
                } else {
                    // Ya tenemos todas las claves expiradas, ahora borrarlas
                    const count = keysToDelete.length;
                    const deletePromises = keysToDelete.map(key => 
                        new Promise((resolveDelete) => {
                            const deleteRequest = store.delete(key);
                            deleteRequest.onsuccess = () => resolveDelete();
                            deleteRequest.onerror = () => resolveDelete();
                        })
                    );
                    
                    Promise.all(deletePromises)
                        .then(() => resolve(count))
                        .catch(() => resolve(count));
                }
            };
            
            request.onerror = () => resolve(0);
        });
    }
    
    /**
     * Obtiene todas las claves almacenadas
     * Si se especifica un prefijo en el constructor, solo devuelve las que tienen ese prefijo
     * @returns {string[]|Promise<string[]>} - Valor o Promesa según el tipo de almacenamiento
     */
    getKeys() {
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._getKeysIndexedDB();
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                const keys = Object.keys(this.storage);
                
                if (!this.prefix) {
                    return keys;
                }
                
                // Filtrar por prefijo y eliminar el prefijo de cada clave
                return keys
                    .filter(key => key.startsWith(this.prefix + '_'))
                    .map(key => key.substring(this.prefix.length + 1));
            }
        } catch (error) {
            console.error('Error al obtener claves:', error);
            return this.type === 'indexed' ? Promise.resolve([]) : [];
        }
    }
    
    /**
     * Implementación privada de getKeys para IndexedDB
     * @returns {Promise<string[]>} - Promesa que se resuelve con un array de claves
     * @private
     */
    async _getKeysIndexedDB() {
        await this._waitForDB();
        
        return new Promise((resolve) => {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const request = store.getAllKeys();
            
            request.onsuccess = (event) => {
                let keys = event.target.result;
                
                if (this.prefix) {
                    // Filtrar por prefijo y eliminar el prefijo de cada clave
                    keys = keys
                        .filter(key => key.startsWith(this.prefix + '_'))
                        .map(key => key.substring(this.prefix.length + 1));
                }
                
                resolve(keys);
            };
            
            request.onerror = () => resolve([]);
        });
    }
    
    /**
     * Obtiene todos los valores almacenados como un objeto
     * @returns {Object|Promise<Object>} - Valor o Promesa según el tipo de almacenamiento
     */
    getAll() {
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._getAllIndexedDB();
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                const result = {};
                const keys = this.getKeys();
                
                keys.forEach(key => {
                    result[key] = this.get(key);
                });
                
                return result;
            }
        } catch (error) {
            console.error('Error al obtener todos los valores:', error);
            return this.type === 'indexed' ? Promise.resolve({}) : {};
        }
    }
    
    /**
     * Implementación privada de getAll para IndexedDB
     * @returns {Promise<Object>} - Promesa que se resuelve con un objeto con todos los valores
     * @private
     */
    async _getAllIndexedDB() {
        const result = {};
        const keys = await this.getKeys();
        
        for (const key of keys) {
            result[key] = await this.get(key);
        }
        
        return result;
    }
    
    /**
     * Actualiza la expiración de una clave existente
     * @param {string} key - Clave a actualizar
     * @param {number} expiry - Nuevo tiempo de expiración en milisegundos
     * @returns {boolean|Promise<boolean>} - Valor o Promesa según el tipo de almacenamiento
     */
    updateExpiry(key, expiry) {
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._updateExpiryIndexedDB(key, expiry);
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                const value = this.get(key);
                
                if (value === null) {
                    return false;
                }
                
                return this.set(key, value, expiry);
            }
        } catch (error) {
            console.error(`Error al actualizar expiración de ${key}:`, error);
            return this.type === 'indexed' ? Promise.resolve(false) : false;
        }
    }
    
    /**
     * Implementación privada de updateExpiry para IndexedDB
     * @param {string} key - Clave a actualizar
     * @param {number} expiry - Nuevo tiempo de expiración en milisegundos
     * @returns {Promise<boolean>} - Promesa que se resuelve con true si se actualizó correctamente
     * @private
     */
    async _updateExpiryIndexedDB(key, expiry) {
        const value = await this.get(key);
        
        if (value === null) {
            return false;
        }
        
        return await this.set(key, value, expiry);
    }
    
    /**
     * Obtiene el tiempo restante de expiración de una clave en milisegundos
     * @param {string} key - Clave a verificar
     * @returns {number|null|Promise<number|null>} - Valor o Promesa según el tipo de almacenamiento
     */
    getTimeToExpiry(key) {
        const prefixedKey = this._getPrefixedKey(key);
        
        try {
            if (this.type === 'indexed') {
                // Manejar IndexedDB (asíncrono)
                return this._getTimeToExpiryIndexedDB(prefixedKey);
            } else {
                // Manejar localStorage/sessionStorage (síncrono)
                const value = this.storage.getItem(prefixedKey);
                
                if (value === null) {
                    return null;
                }
                
                try {
                    const parsed = JSON.parse(value);
                    
                    if (this._hasExpiry(parsed)) {
                        const now = new Date().getTime();
                        const timeElapsed = now - parsed.timestamp;
                        const timeRemaining = parsed.expiry - timeElapsed;
                        
                        return timeRemaining > 0 ? timeRemaining : 0;
                    }
                } catch (e) {
                    // Ignorar errores de parsing
                }
                
                return null; // No tiene expiración
            }
        } catch (error) {
            console.warn(`Error al obtener tiempo de expiración de ${prefixedKey}:`, error);
            return this.type === 'indexed' ? Promise.resolve(null) : null;
        }
    }
    
    /**
     * Implementación privada de getTimeToExpiry para IndexedDB
     * @param {string} prefixedKey - Clave con prefijo
     * @returns {Promise<number|null>} - Promesa que se resuelve con el tiempo restante
     * @private
     */
    async _getTimeToExpiryIndexedDB(prefixedKey) {
        await this._waitForDB();
        
        return new Promise((resolve) => {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const request = store.get(prefixedKey);
            
            request.onsuccess = (event) => {
                const result = event.target.result;
                
                if (!result) {
                    resolve(null);
                    return;
                }
                
                try {
                    const parsed = JSON.parse(result.value);
                    
                    if (this._hasExpiry(parsed)) {
                        const now = new Date().getTime();
                        const timeElapsed = now - parsed.timestamp;
                        const timeRemaining = parsed.expiry - timeElapsed;
                        
                        resolve(timeRemaining > 0 ? timeRemaining : 0);
                        return;
                    }
                } catch (e) {
                    // Ignorar errores de parsing
                }
                
                resolve(null);
            };
            
            request.onerror = () => resolve(null);
        });
    }
    
    /**
     * Almacena un archivo en IndexedDB
     * @param {string} key - Clave para el archivo
     * @param {Blob|File} file - Archivo a almacenar
     * @param {number|null} expiry - Tiempo de expiración en milisegundos
     * @returns {Promise<boolean>} - Promesa que se resuelve con true si se guardó correctamente
     */
    async storeFile(key, file, expiry = null) {
        // Solo disponible en IndexedDB
        if (this.type !== 'indexed') {
            console.warn('storeFile solo está disponible para IndexedDB');
            return false;
        }
        
        return this.set(key, file, expiry);
    }
    
    /**
     * Obtiene un archivo almacenado en IndexedDB
     * @param {string} key - Clave del archivo
     * @returns {Promise<Blob|null>} - Promesa que se resuelve con el archivo o null si no existe
     */
    async getFile(key) {
        // Solo disponible en IndexedDB
        if (this.type !== 'indexed') {
            console.warn('getFile solo está disponible para IndexedDB');
            return null;
        }
        
        return this.get(key, null);
    }
    
    /**
     * Obtiene información sobre el uso del almacenamiento
     * @returns {Promise<Object>} - Promesa que se resuelve con información de uso
     */
    async getStorageInfo() {
        try {
            const keys = await this.getKeys();
            const itemCount = keys.length;
            let totalSize = 0;
            
            if (this.type === 'indexed') {
                // Obtener tamaño aproximado para IndexedDB
                await this._waitForDB();
                
                return new Promise(async (resolve) => {
                    try {
                        const all = await this.getAll();
                        totalSize = new Blob([JSON.stringify(all)]).size;
                    } catch (e) {
                        totalSize = 0;
                    }
                    
                    resolve({
                        type: this.storageType,
                        itemCount,
                        totalSize,
                        unit: 'bytes'
                    });
                });
            } else {
                // localStorage/sessionStorage
                const all = await this.getAll();
                totalSize = new Blob([JSON.stringify(all)]).size;
                
                return {
                    type: this.storageType,
                    itemCount,
                    totalSize,
                    unit: 'bytes'
                };
            }
        } catch (error) {
            console.error('Error al obtener información del almacenamiento:', error);
            return {
                type: this.storageType,
                itemCount: 0,
                totalSize: 0,
                unit: 'bytes',
                error: error.message
            };
        }
    }
}

// Exportar como módulo ES6
export default Storage;
