        </main>
    </div>
</div>

<!-- JavaScript code - Mobile menu toggle functionality -->
<script>
    // ===== MOBILE MENU FUNCTIONALITY =====
    // Mobile devices par menu ko toggle karne ke liye JavaScript code
    // Desktop par menu pehle se visible hota hai
    // Mobile par hamburger icon se menu open/close hota hai
    
    // getElementById() se HTML element ko JavaScript mein access karte hain
    // 'mobileMenuToggle' = hamburger button (menu icon)
    // 'mobileSidebar' = navigation menu
    const menuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('mobileSidebar');

    // Check karo ke dono elements exist karte hain ya nahi
    // && matlab both conditions true hone chahiye
    if(menuToggle && sidebar){
        // addEventListener() function se event listener attach karte hain
        // 'click' event = jab user button par click kare
        // function() = code jo click hone par chale
        menuToggle.addEventListener('click', function(){
            // classList.toggle() se class ko add/remove karte hain
            // 'active' class add ho to active ban jata hai
            // 'active' class remove ho to inactive ban jata hai
            menuToggle.classList.toggle('active');
            sidebar.classList.toggle('active');
        });

        // ===== Close menu when a link is clicked =====
        // Jab user menu mein koi link click kare to menu close ho jaye
        
        // querySelectorAll() se sidebar ke andar sab links ko select karo
        const sidebarLinks = sidebar.querySelectorAll('a');
        
        // forEach() loop se har link par function apply karo
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(){
                // Link click hone par 'active' class remove karo
                // Remove karne se menu close ho jayega
                menuToggle.classList.remove('active');
                sidebar.classList.remove('active');
            });
        });

        // ===== Close menu when clicking outside =====
        // Jab user menu ke bahar click kare to menu close ho jaye
        
        // Document ke har click par ye function chalega
        document.addEventListener('click', function(event){
            // event.target = jo element click hua
            // contains() check karta hai ke element iske andar hai ya nahi
            // isClickInside = agar click menu ya button par hua to true
            const isClickInside = sidebar.contains(event.target) || menuToggle.contains(event.target);
            
            // Agar click menu ke bahar hua AND menu already open hai
            // to menu close karo
            if(!isClickInside && sidebar.classList.contains('active')){
                menuToggle.classList.remove('active');
                sidebar.classList.remove('active');
            }
        });
    }
</script>
