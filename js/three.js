
// This is test file for three.js since we use dynamic loading this file will be ignored for now 

// Create a scene
var scene = new THREE.Scene();

// Create a camera
var camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);

// Create a renderer and add it to the DOM
var renderer = new THREE.WebGLRenderer();
renderer.setSize(window.innerWidth, window.innerHeight);
document.body.appendChild(renderer.domElement);

// Update the renderer and the camera when the window is resized
window.addEventListener('resize', function() {
    var width = window.innerWidth;
    var height = window.innerHeight;
    renderer.setSize(width, height);
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
});

// Create a GLTF loader
var loader = new THREE.GLTFLoader();

// Load a glTF resource
loader.load(
    // URL of the 3D model file
    'path_to_your_3d_model_file.gltf',
    
    // Function when resource is loaded
    function(gltf) {
        scene.add(gltf.scene);
        
        // Set camera position so that we can see the object
        camera.position.z = 5;
    },
    
    // Function called while download is in progress
    function(xhr) {
        console.log((xhr.loaded / xhr.total * 100) + '% loaded');
    },
    
    // Function called when download errors
    function(error) {
        console.log('An error happened', error);
    }
);

// Create an animation loop
function animate() {
    requestAnimationFrame(animate);
    
    // Render the scene with the camera
    renderer.render(scene, camera);
}

// Start the animation loop
animate();
