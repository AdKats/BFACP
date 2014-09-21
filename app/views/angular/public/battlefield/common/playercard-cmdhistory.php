<div class="table-responsive">
    <table class="table table-condensed table-striped">
        <thead>
            <th width="50%">Command</th>
            <th>Used</th>
        </thead>

        <tbody>
            <tr ng-repeat="command in commandusage | orderBy: '-y'">
                <td>{{command.name}}</td>
                <td>{{command.y | number}}</td>
            </tr>
        </tbody>
    </table>
</div>
