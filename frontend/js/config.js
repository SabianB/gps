const domainName = `${window.location.origin}/`;
const folderPath = 'gps';
const config = {
    'serverApi': `${domainName}${folderPath}/backend/api.php`,
    'uploadApi': `${domainName}${folderPath}/backend/api/upload.php`,
    'webHome': `${domainName}${folderPath}/frontend`,
    'resources': `${domainName}${folderPath}/backend/api/media.php?codigo=`,
    'jsonPath': `${domainName}${folderPath}/frontend/`
};