<div class="text-base">
    <h2 class="text-3xl font-bold mb-6 text-heading">Help</h2>

    <h3 class="text-xl font-medium mb-4">Introduction</h3>
    <p>
        This application allows to display results of solvers on different combinatorial problems.
        On the left, you have a panel for applying some filters (described below) and one for selecting
        solvers. On the top right, you can select whichever view you prefer</p>
    <ul class="ml-4 space-y-1 list-disc list-inside mb-8">
        <li>A detailed view: summary, cactus plot and results for all selected instances.</li>
        <li>A versus view: You select 2 solvers, and you perform a one to one comparison with a scatter plots and to
            charts related to specific constraints and specific families.
        </li>
        <li>A radar view: for each family, you can see the number of solvers instances by each solver.</li>
    </ul>

    <h3 class="text-xl font-medium mb-4">Filters</h3>
    <p class="mb-2">The icon <span>@svg('heroicon-m-bars-3', 'text-green-300 inline w-4 h-4')</span> allows to
        hide/display filters.
    </p>
    <p class="mb-2">Different filters can be applied to refine the presentation of results (which are then re-computed
        accordingly):</p>
    <ul class="ml-4 space-y-1 list-disc list-inside mb-4">
        <li>Problems: You can filter by family of problems.
        </li>
        <li>Constraints: You can forbid one/several constraints or force all problems to contain a type of
            constraint(s) (for CSP, COP evaluations).
        </li>
        <li>For CSP, with a dedicated pair of buttons, you can select all instances or only known SAT or UNSAT ones.
        </li>
        <li>For COP, with a dedicated pair of buttons, you can select all instances or only instances that must be
            maximized or minimized.
        </li>
        <li>For COP, with a dedicated pair of buttons, you can also select all instances or only open instances or
            instances with known optima.
        </li>
        <li>Expression: logical rules can be applied with respect to the number of variables (v), the number of
            constraints (c), the maximal domain size(d). For
            example:
            <ul class="ml-6 text-blue-300 space-y-1 list-disc list-inside mb-4">
                <li>v > 100: select only instances with at least 100 variables.</li>
                <li>v > 100 && c > 1000: select only instances with at least 100 variables and 1000 constraints.</li>
                <li>d == 2: select only binary problems.</li>
            </ul>
        </li>
    </ul>
    You can also reduce the maximum time allowed to solve an instance.
    <br/><br/><br/>

    <h3 class="h3">Information related to CSP/SAT instances</h3>

    <h4 class="text-lg">Table view</h4>
    <ul class="ml-4 space-y-1 list-disc list-inside mb-2">
        <li>A table summarising results per solver S: number of solved instances (Solved), number of satisfiable and
            unsatisfiable
            solved instances (SAT/UNSAT), number of times S is the only solver to solve an instance, number of times S
            is the fastest
            solver, and finally the Par2 score of S. Note that the Virtual Best Solver (wrt the selected solvers) is
            also displayed.
        </li>
        <li>A cactus plot</li>
        <li>A table providing detailed results per instance and per solver. For each instance, we display first its
            name, its number
            of involved variables (V) and constraints (C) and its status (if known). Then, the time required by
            selected solvers
            to solve the instance is displayed. Each time a solver is the best one on a given instance, its
            corresponding running time is shown in green.
        </li>
    </ul>

    <p class="mb-8">Note that you can sort the tables by clicking on the column names.</p>

    <h3 class="h3"> Information related to COP instances</h3>
    <h4 class="text-lg">Table view</h4>
    <ul class="ml-4 space-y-1 list-disc list-inside mb-4">
        <li>A table summarising results per solver S:
            the score of S,
            the number of times S is able to prove optimality (#Opt),
            the number of times S finds the best bound wrt the selection while S not proving optimality and no other
            selected solver proving optimality (#BB1),
            the number of times S finds the best bound wrt the selection while S not proving optimality but another
            selected solver proving optimaliry (#BB2).
            The score is computed as: #Opt + #BB1 + #BB2/2.
            The Virtual Best Solver is also displayed in the table.
        </li>
        <li>
            A cactus plot showing a proof-oriented vision (an instance is considered as solved by a solver S when it is
            proven to optimality), and a cactus plot showing a search-oriented vision (an instance is considered as
            solved when the best bound found by any solver of the selection is reached; the proof of optimality is
            discarded).
        </li>
        <li>A table providing detailed results per instance and per solver. For each instance, we display first its
            name, its number
            of involved variables (V) and constraints (C), the type of its objective and the best bound found so far
            (if this
            bound is known to be optimal, it is shown in green). Then, for each selected solver S, you can find the
            obtained bound
            (and its score between brackets), and on the line below, the time taken to find the bound as well as the
            time taken to
            prove optimality (when appropriate; in such a case, results are shown in green). By clicking on the instance
            name, you will get informations about it (type of constraints, degree of
            variables...).
        </li>
        <li>Note that when you click on a line, the behaviour (sequence of found bounds) of selected solvers is ploted
            in a pop-up window.
        </li>
    </ul>


</div>
