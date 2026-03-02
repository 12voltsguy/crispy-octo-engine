<!doctype html>
<html class="h-full bg-gray-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans h-full">
<!-- This example requires Tailwind CSS v2.0+ -->
<!--
  This example requires updating your template:

  ```
  <html class="h-full bg-gray-100">
  <body class="h-full">
  ```
-->
<div class="min-h-full">
  <nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <h1 class="text-3xl font-bold text-sky-500">Alertiv</h1>
          </div>
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">
              <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
              <a href="#" class="bg-gray-900 text-white px-3 py-2 rounded-md text-sm font-medium" aria-current="page">Settings</a>
            </div>
          </div>
        </div>
        
      </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="md:hidden" id="mobile-menu">
      <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
        <a href="#" class="bg-gray-900 text-white block px-3 py-2 rounded-md text-base font-medium" aria-current="page">Settings</a>
      </div>

    </div>
  </nav>

  <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Settings</h1>
    </div>
  </header>
  <main>
  <div class="flex justify-center">
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
      <?php
          include 'db.php';

          $rcareAccounts = DB::query("SELECT * FROM rcare_account");
          echo("<div class=\"py-4\">
                  <h2 class=\"text-lg\">Rcare Accounts</h2>
                  <button id=\"refreshAccount\" class=\"my-4 px-4 py-2 font-semibold text-sm bg-cyan-500 text-white rounded-full shadow-sm\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"currentColor\" class=\"inline mr-2 w-6 h-6\">
                  <path fill-rule=\"evenodd\" d=\"M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z\" clip-rule=\"evenodd\" />
                </svg>Refresh Rcare Accounts</button>
                  <table class=\"table-auto border-collapse border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 text-sm shadow-sm\">
                      <tr>
                          <th class=\"p-2 border border-slate-300 dark:border-slate-600 font-semibold text-slate-900 dark:text-slate-200 text-left\">Id</th>
                          <th class=\"p-2 border border-slate-300 dark:border-slate-600 font-semibold text-slate-900 dark:text-slate-200 text-left\">Name</th>
                          <th class=\"p-2 border border-slate-300 dark:border-slate-600 font-semibold text-slate-900 dark:text-slate-200 text-left\">Address1</th>
                          <th class=\"p-2 border border-slate-300 dark:border-slate-600 font-semibold text-slate-900 dark:text-slate-200 text-left\">Address2</th>
                      </tr>
                          "
          );

          foreach($rcareAccounts as $account){
              echo("
                  <tr>
                      <td class=\"px-2 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400\">" . $account["account"] . "</td>
                      <td class=\"px-2 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400\">" . $account["name"] . "</td>
                      <td class=\"px-2 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400\">" . $account["address1"] . "</td>
                      <td class=\"px-2 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400\">" . $account["address2"] . "</td>
                  </tr>
              ");
          }

          echo("
                  </table>
              </div>"
        );
        
      ?>
      <div class="py-4">
        <h2 class="text-lg">Log Entries</h2>
          <button id="refreshLogs" class="my-4 px-4 py-2 font-semibold text-sm bg-cyan-500 text-white rounded-full shadow-sm"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="inline mr-2 w-6 h-6">
  <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z" clip-rule="evenodd" />
</svg>
Refresh Logs</button>
          <iframe class="overflow-auto" id="logs" src="logentries.php?top=200" width=1000 height=400 frameborder=0 ></iframe>
      </div>
    </div>
    </div>
  </main>
</div>
</body>

<script>
  function refreshAccount(){
    result = fetch('refreshAccounts.php');
    if(result){
      alert("Refreshed successfully!");
    }
  };

  function refreshLogs(){
    var iframe = document.getElementById('logs');
    iframe.src = iframe.src;
  }


  document.getElementById("refreshAccount").addEventListener("click", refreshAccount);
  document.getElementById("refreshLogs").addEventListener("click", refreshLogs);
</script>

</html>